<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Task;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    // Membuat visit baru
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id', // Pastikan task_id ada di database
        ]);

        $user = auth()->user();

        // Cari task dan validasi apakah task milik reseller dari user ini
        $task = Task::with('reseller')
            ->where('id', $request->task_id)
            ->whereHas('reseller', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Task tidak valid untuk pengguna ini'], 403);
        }

        // Buat visit baru
        $visit = Visit::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'status' => 'pending', // Default status
        ]);

        return response()->json(['message' => 'Visit berhasil dibuat', 'visit' => $visit], 201);
    }

    // Konfirmasi visit
    public function confirm($visit_id)
    {
        $user = auth()->user();

        // Cari visit berdasarkan user yang login
        $visit = Visit::with('task.reseller')
            ->where('id', $visit_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$visit) {
            return response()->json(['message' => 'Visit tidak ditemukan atau tidak valid untuk pengguna ini'], 403);
        }

        // Konfirmasi visit
        $visit->update(['status' => 'completed']);

        return response()->json(['message' => 'Visit berhasil dikonfirmasi', 'visit' => $visit], 200);
    }
}
