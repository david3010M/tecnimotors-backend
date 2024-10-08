<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attention;
use App\Models\DetailAttention;
use App\Models\RouteImages;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/task",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="Store a task",
     *     description="Store a task",
     *     operationId="storeTask",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/TaskRequest")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="La fecha Límite de la Tarea debe ser menor que la Fecha de entrega del Vehículo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'description' => 'required',
            'registerDate' => 'nullable|date',
            'limitDate' => 'nullable|date',
            'detail_attentions_id' => 'required|exists:detail_attentions,id',
        ]);

        $detailAttention = DetailAttention::find($request->input('detail_attentions_id'));
        $attentionDate = $detailAttention->dateMax;
        $limitDate = ($request->input('limitDate')) ? strtotime($request->input('limitDate')) : null;

        if ($limitDate && $limitDate > strtotime($attentionDate)) {
            return response()->json(['error' => 'La fecha Límite de la Tarea debe ser menor que la Fecha de entrega del Vehículo'], 422);
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'description' => $request->input('description'),
            'status' => 'hacer',
            'registerDate' => $request->input('registerDate') ?? now(),
            'limitDate' => $request->input('limitDate') ?? $attentionDate,
            'worker_id' => $detailAttention->worker_id,
            'detail_attentions_id' => $request->input('detail_attentions_id'),
        ];

        $task = Task::create($data);
        $task = Task::with('worker', 'detailAttentions', 'routeImages')->find($task->id);

        return response()->json($task);
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/task/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="Show a task",
     *     description="Show a task",
     *     operationId="showTask",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of task",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $task = Task::with('worker', 'detailAttentions', 'routeImages')->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json($task);
    }

    /**
     * @OA\Put (
     *     path="/tecnimotors-backend/public/api/task/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="Update a task",
     *     description="Update a task",
     *     operationId="updateTask",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of task",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/TaskUpdate")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="La fecha Límite de la Tarea debe ser menor que la Fecha de entrega del Vehículo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'description' => 'nullable',
            'status' => 'nullable|string|in:hacer,curso,listo',
            'limitDate' => 'nullable|date',
        ]);

        if ($request->input('limitDate')) {
            $limitDate = strtotime($request->input('limitDate'));
            $attentionDate = strtotime($task->detailAttentions->dateMax);
            $attentionRegisterDate = strtotime($task->detailAttentions->dateRegister);

            if ($limitDate > $attentionDate) {
                return response()->json(['error' => 'La fecha Límite de la Tarea debe ser menor que la Fecha de entrega del Vehículo '
                    . Carbon::parse($attentionDate)->format('Y-m-d')], 422);
            }

            if ($limitDate < $attentionRegisterDate) {
                return response()->json(['error' => 'The limit date must be greater than the attention register date '
                    . Carbon::parse($attentionRegisterDate)->format('Y-m-d')], 422);
            }
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'description' => $request->input('description') ?? $task->description,
            'status' => $request->input('status') ?? $task->status,
            'limitDate' => $request->input('limitDate') ?? $task->limitDate,
        ];

        $task->update($data);
        $task = Task::with('worker', 'detailAttentions', 'routeImages')->find($task->id);

        return response()->json($task);
    }

    /**
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/task/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="Delete a task",
     *     description="Delete a task",
     *     operationId="destroyTask",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of task",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $idWorker = auth()->user()->worker->id;
        $task = Task::find($id);

        if (!$task || $task->worker_id !== $idWorker) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/taskByDetailAttention/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="List all Tasks by Detail Attention",
     *     description="List all Tasks by Detail Attention",
     *     operationId="taskByDetailAttention",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of detail attention",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TaskNoRelations")
     *         )
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tasks not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Tasks not found")
     *         )
     *     )
     * )
     */
    public function getTaskByDetailAttention(int $id)
    {
//        $tasks = Task::with('worker', 'detailAttentions')->where('detail_attentions_id', $id)->get();
        $tasks = Task::with('worker', 'detailAttentions', 'routeImages')->where('detail_attentions_id', $id)->get();

        return response()->json($tasks);
    }

    /**
     * @OA\Post (
     *     path="/tecnimotors-backend/public/api/taskEvidence/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="Store the evidence of a task",
     *     description="Store the evidence of a task",
     *     operationId="storeEvidence",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of task",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      type="array",
     *                      property="routeImage[]",
     *                      @OA\Items(type="file", format="binary")
     *                  )
     *             )
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="url", type="string", example="http://localhost:8000/storage/photosTaskEvidence/1/image.jpg"),
     *             @OA\Property(property="task_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function storeEvidence(Request $request, int $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $images = $request->file('routeImage') ?? [];
        $index = 1;

        foreach ($images as $image) {
            $file = $image;

            $currentTime = now();

            // Reemplazar espacios por guiones bajos en el nombre original del archivo
            $originalName = str_replace(' ', '_', $file->getClientOriginalName());

            $filename = $index . '-' . $currentTime->format('YmdHis') . '_' . $originalName;
            $path = $file->storeAs('public/photosTaskEvidence', $filename);
            $routeImage = 'https://develop.garzasoft.com/tecnimotors-backend/storage/app/' . $path;

            $index++;

            $dataImage = [
                'route' => $routeImage,
                'task_id' => $task->id,
                'attention_id' => $task->detailAttentions->attention_id,
            ];
            RouteImages::create($dataImage);
        }

        $routes = RouteImages::where('task_id', $task->id)->get();

        return response()->json($routes);
    }

    /**
     * @OA\Delete (
     *     path="/tecnimotors-backend/public/api/taskEvidence/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="Delete an evidence",
     *     description="Delete an evidence",
     *     operationId="deleteEvidence",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID of the evidence", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Delete an evidence", @OA\JsonContent(@OA\Property(property="message", type="string", example="Evidence deleted"))),
     *     @OA\Response(response="404", description="Error: Not Found", @OA\JsonContent(@OA\Property(property="message", type="string", example="Evidence not found")))
     * )
     */
    public function deleteEvidence(int $id)
    {
        $routeImage = RouteImages::find($id);
        if (!$routeImage) return response()->json(['message' => 'Evidence not found'], 404);
        $routeImage->delete();
        return response()->json(['message' => 'Evidence deleted']);
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/taskEvidence/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="List all evidence of a task",
     *     description="List all evidence of a task",
     *     operationId="listEvidence",
     *     @OA\Parameter( name="id", in="path", required=true, description="ID of the task", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="List all evidence of a task", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/RouteImages"))),
     *     @OA\Response(response="404", description="Task not found", @OA\JsonContent(@OA\Property(property="message", type="string", example="Task not found"))),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated.")))
     * )
     */
    public function listEvidence(int $id)
    {
        $task = Task::find($id);
        if (!$task) return response()->json(['message' => 'Task not found'], 404);
        $routes = RouteImages::where('task_id', $task->id)->get();
        return response()->json($routes);
    }

    /**
     * @OA\Get (
     *     path="/tecnimotors-backend/public/api/taskEvidenceByAttention/{id}",
     *     tags={"Task"},
     *     security={{"bearerAuth": {}}},
     *     summary="List all evidence of a attention",
     *     description="List all evidence of a attention",
     *     operationId="listEvidenceByAttention",
     *     @OA\Parameter( name="id", in="path", required=true, description="ID of the attention", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="List all evidence of a attention", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/RouteImages"))),
     *     @OA\Response(response="404", description="Attention not found", @OA\JsonContent(@OA\Property(property="message", type="string", example="Attention not found"))),
     *     @OA\Response(response="401", description="Unauthenticated", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated.")))
     * )
     */
    public function listEvidenceByAttention(int $id)
    {
        $attention = Attention::find($id);
        if (!$attention) return response()->json(['message' => 'Attention not found'], 404);
        $routes = RouteImages::where('attention_id', $attention->id)->get();
        return response()->json($routes);
    }

}
