<?php

namespace App\Services;

use App\Models\ConceptMov;
use App\Models\DocAlmacen;
use App\Models\Docalmacen_details;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DocAlmacenService
{
    public function generate(array $budgetProductLines, $attention, $budget, $concept_id='3'): DocAlmacen
    {
        try {
            $map = $this->normalizeLines($budgetProductLines);
            if (empty($map)) {
                throw new \InvalidArgumentException('No hay productos válidos para generar el DocAlmacén.');
            }

            return DB::transaction(function () use ($map, $attention, $budget, $concept_id) {
                $existing = DocAlmacen::where('budget_sheet_id', $budget->id)->lockForUpdate()->first();
                if ($existing) {
                    return $existing->load('details');
                }

                $conceptMov = ConceptMov::findOrFail($concept_id);
                $totalQuantity = array_sum($map);

                $doc = DocAlmacen::create([
                    'sequentialnumber' => $this->nextCorrelative(DocAlmacen::class, 'sequentialnumber'),
                    'date_moviment'    => $attention->deliveryDate ?? now(),
                    'quantity'         => $totalQuantity,
                    'comment'          => 'Salida de productos por Presupuesto ' . ($budget->number ?? ''),
                    'typemov'          => DocAlmacen::TIPOMOV_EGRESO,
                    'concept'          => $conceptMov->name,
                    'user_id'          => Auth::id(),
                    'person_id'        => $attention->vehicle?->person?->id,
                    'concept_mov_id'   => $conceptMov->id,
                    'attention_id'     => $attention->id ?? null,
                    'budget_sheet_id'  => $budget->id ?? null,
                ]);

                foreach ($map as $productId => $qty) {
                    /** @var Product $product */
                    $product = Product::query()->lockForUpdate()->findOrFail((int)$productId);

                    if ($product->stock < $qty) {
                        throw new \RuntimeException(
                            "Stock insuficiente para el producto ID {$product->id}. Stock: {$product->stock}, requerido: {$qty}"
                        );
                    }

                    Docalmacen_details::create([
                        'sequentialnumber' => $this->nextCorrelative(Docalmacen_details::class, 'sequentialnumber'),
                        'quantity'         => (int)$qty,
                        'comment'          => 'Detalle salida por Presupuesto ' . ($budget->number ?? ''),
                        'product_id'       => (int)$productId,
                        'doc_almacen_id'   => $doc->id,
                    ]);

                    $product->decrement('stock', (int)$qty);
                }

                return $doc->load('details');
            });
        } catch (Throwable $e) {
            Log::error('Error al generar DocAlmacén: ' . $e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'budget'  => $budget->id ?? null,
                'details' => $budgetProductLines ?? [],
            ]);
            throw $e;
        }
    }

    public function update(array $detailsProducts, $object, $budget = null): DocAlmacen
    {
        try {
            $incoming = $this->normalizeLines($detailsProducts);
            if (empty($incoming)) {
                throw new \InvalidArgumentException('No se enviaron productos válidos para actualizar.');
            }

            return DB::transaction(function () use ($incoming, $object, $budget) {
                $query = DocAlmacen::query()->lockForUpdate();
                if ($budget) {
                    $query->where('budget_sheet_id', $budget->id);
                } else {
                    $query->where('attention_id', $object->id);
                }

                $doc = $query->first();

                if (!$doc) {
                    return $this->generate(
                        budgetProductLines: $this->toList($incoming),
                        attention: $object,
                        budget: $budget
                    );
                }

                $existing = $doc->details()
                    ->select('product_id', DB::raw('SUM(quantity) as quantity'))
                    ->groupBy('product_id')
                    ->get()
                    ->keyBy('product_id')
                    ->map(fn($r) => (int)$r->quantity)
                    ->toArray();

                $allIds = array_unique(array_merge(array_keys($incoming), array_keys($existing)));

                foreach ($allIds as $pid) {
                    $newQty = (int)($incoming[$pid] ?? 0);
                    $oldQty = (int)($existing[$pid] ?? 0);
                    if ($newQty === $oldQty) continue;

                    /** @var Product $product */
                    $product = Product::query()->lockForUpdate()->findOrFail((int)$pid);

                    if ($newQty > $oldQty) {
                        $diff = $newQty - $oldQty;
                        if ($product->stock < $diff) {
                            throw new \RuntimeException("Stock insuficiente para el producto ID {$product->id}. Stock: {$product->stock}, requerido adicional: {$diff}");
                        }
                        $this->upsertDetailQuantity($doc, (int)$pid, $diff, 'Ajuste (+) Salida');
                        $product->decrement('stock', $diff);
                    } else {
                        $diff = $oldQty - $newQty;
                        $this->reduceDetailQuantity($doc, (int)$pid, $diff, 'Ajuste (-) Salida');
                        $product->increment('stock', $diff);
                    }
                }

                $doc->quantity        = (int)$doc->details()->sum('quantity');
                $doc->date_moviment   = $object->deliveryDate ?? $doc->date_moviment;
                $doc->person_id       = $object->vehicle?->person?->id;
                $doc->budget_sheet_id = $budget->id ?? $doc->budget_sheet_id;
                $doc->save();

                return $doc->load('details');
            });
        } catch (Throwable $e) {
            Log::error('Error al actualizar DocAlmacén: ' . $e->getMessage(), [
                'trace'     => $e->getTraceAsString(),
                'budget_id' => $budget->id ?? null,
                'object_id' => $object->id ?? null,
                'details'   => $detailsProducts ?? [],
            ]);
            throw $e;
        }
    }

    private function normalizeLines(array $rows): array
    {
        $map = [];
        foreach ($rows as $row) {
            $pid = (int)($row['product_id'] ?? $row['idProduct'] ?? 0);
            if ($pid <= 0) continue;
            $qty = max(1, (int)($row['quantity'] ?? 1));
            $map[$pid] = ($map[$pid] ?? 0) + $qty;
        }
        return $map;
    }

    private function toList(array $map): array
    {
        $list = [];
        foreach ($map as $pid => $qty) {
            $list[] = ['product_id' => (int)$pid, 'quantity' => (int)$qty];
        }
        return $list;
    }

    protected function upsertDetailQuantity(DocAlmacen $doc, int $productId, int $qty, string $comment): void
    {
        $detail = $doc->details()->where('product_id', $productId)->first();
        if ($detail) {
            $detail->quantity += $qty;
            $detail->comment   = $comment;
            $detail->save();
            return;
        }

        Docalmacen_details::create([
            'sequentialnumber' => $this->nextCorrelative(Docalmacen_details::class, 'sequentialnumber'),
            'quantity'         => $qty,
            'comment'          => $comment,
            'product_id'       => $productId,
            'doc_almacen_id'   => $doc->id,
        ]);
    }

    protected function reduceDetailQuantity(DocAlmacen $doc, int $productId, int $qty, string $comment): void
    {
        $detail = $doc->details()->where('product_id', $productId)->first();
        if (!$detail) return;

        $newQty = (int)$detail->quantity - $qty;

        if ($newQty > 0) {
            $detail->quantity = $newQty;
            $detail->comment  = $comment;
            $detail->save();
        } else {
            $detail->delete();
        }
    }

    protected function nextCorrelative(string $modelClass, string $column): string
    {
        $last = $modelClass::query()->max($column);
        $num  = (int)preg_replace('/\D/', '', (string)$last) + 1;
        return str_pad((string)$num, 8, '0', STR_PAD_LEFT);
    }
}
