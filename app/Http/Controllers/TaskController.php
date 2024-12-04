<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Reseller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tasks = Task::with('reseller')
            ->whereHas('reseller', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();

        return response()->json($tasks, 200);
    }

    public function store(Request $req)
    {
        $req->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'task' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $user = auth()->user();
        $reseller = Reseller::where('id', $req->reseller_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$reseller) {
            return response()->json(['message' => 'Reseller tidak ditemukan'], 404);
        }

        $task = Task::create([
            'reseller_id' => $reseller->id,
            'task' => $req->task,
            'description' => $req->description,
        ]);

        return response()->json(['message' => 'Task berhasil ditambahkan', 'task' => $task], 201);
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'task' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $task = Task::with('reseller')
            ->where('id', $id)
            ->whereHas('reseller', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        $task->update([
            'task' => $req->task,
            'description' => $req->description,
        ]);

        return response()->json(['message' => 'Task berhasil diperbarui', 'task' => $task], 200);
    }

    public function destroy($id)
    {
        $task = Task::with('reseller')
            ->where('id', $id)
            ->whereHas('reseller', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task berhasil dihapus'], 200);
    }
}
