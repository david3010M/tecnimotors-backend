<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Task::with('worker', 'detailAttention')->simplePaginate(15));
    }

    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'description' => 'required|string',
            'status' => 'required|boolean',
            'percentage' => 'required|integer',
            'dateRegister' => 'required|date',
            'dateStart' => 'required|date',
            'dateEnd' => 'required|date',
            'worker_id' => 'required|exists:workers,id',
            'detail_attentions_id' => 'required|exists:detail_attentions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'percentage' => $request->input('percentage'),
            'dateRegister' => $request->input('dateRegister'),
            'dateStart' => $request->input('dateStart'),
            'dateEnd' => $request->input('dateEnd'),
            'worker_id' => $request->input('worker_id'),
            'detail_attentions_id' => $request->input('detail_attentions_id'),
        ];

        $task = Task::create($data);
        $task = Task::with('worker', 'detailAttentions')->find($task->id);

        return response()->json($task);
    }

    public function show(int $id)
    {
        $task = Task::with('worker', 'detailAttention')->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json($task);
    }

    public function update(Request $request, int $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'description' => 'required|string',
            'status' => 'required|boolean',
            'percentage' => 'required|integer',
            'dateRegister' => 'required|date',
            'dateStart' => 'required|date',
            'dateEnd' => 'required|date',
            'worker_id' => 'required|exists:workers,id',
            'detailAttention_id' => 'required|exists:detail_attentions,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'percentage' => $request->input('percentage'),
            'dateRegister' => $request->input('dateRegister'),
            'dateStart' => $request->input('dateStart'),
            'dateEnd' => $request->input('dateEnd'),
            'worker_id' => $request->input('worker_id'),
            'detailAttention_id' => $request->input('detailAttention_id'),
        ];

        $task->update($data);
        $task = Task::with('worker', 'detailAttention')->find($task->id);

        return response()->json($task);
    }

    public function destroy(int $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    public function getTaskByDetailAttention(int $id)
    {
        $tasks = Task::with('worker', 'detailAttentions')->where('detail_attentions_id', $id)->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Tasks not found'], 404);
        }

        return response()->json($tasks);
    }

    public function storeEvidence(Request $request, int $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'images64' => 'array',
            'images64.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $images = $request->input('images64');
        $path = 'public/images/tasks/' . date('Y-m-d');
        $path = storage_path($path);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        foreach ($images as $key => $image) {
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = date('Y-m-d') . '-' . time() . '-' . $key . '.png';
            $pathImage = $path . '/' . $imageName;
            file_put_contents($pathImage, base64_decode($image));
        }

        $task->images()->create(['path' => $pathImage]);

        return response()->json(['message' => 'Evidence created']);
    }
}
