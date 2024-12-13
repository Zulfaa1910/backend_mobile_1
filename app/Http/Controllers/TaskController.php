<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tasks = Task::where('user_sales_id', $user->id)
            ->with('reseller')
            ->get();

        return response()->json($tasks, 200);
    }

    public function store(Request $req)
    {
        $req->validate([
            'task' => 'required|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|in:warehouse,maintenance,reseller', // Ensure the assigned role is valid
        ]);

        $user = auth()->user();
        $reseller = null;
        
        // If the task is for a reseller, find the corresponding reseller
        if ($req->assigned_to == 'reseller') {
            $req->validate(['reseller_id' => 'required|exists:resellers,id']);
            $reseller = Reseller::where('id', $req->reseller_id)
                ->where('user_sales_id', $user->id)
                ->first();
            
            if (!$reseller) {
                return response()->json(['message' => 'Reseller tidak ditemukan'], 404);
            }
        }

        // Create the task
        $task = Task::create([
            'task' => $req->task,
            'description' => $req->description,
            'user_sales_id' => $user->id,
            'assigned_to' => $req->assigned_to,
            'status' => 'pending', // Default status is pending
            'reseller_id' => $reseller ? $reseller->id : null,
        ]);

        return response()->json(['message' => 'Task berhasil ditambahkan', 'task' => $task], 201);
    }

    public function update(Request $req, $id)
    {
        $req->validate([
            'task' => 'required|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|in:warehouse,maintenance,reseller', // Ensure valid role
        ]);

        $task = Task::find($id);
        
        if (!$task || $task->user_sales_id != auth()->user()->id) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        // Update task
        $task->update([
            'task' => $req->task,
            'description' => $req->description,
            'assigned_to' => $req->assigned_to ?? $task->assigned_to,
        ]);

        return response()->json(['message' => 'Task berhasil diperbarui', 'task' => $task], 200);
    }

    public function markAsCompleted(Request $req, $id)
    {
        $task = Task::find($id);
        
        if (!$task) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        // Validate that a photo has been uploaded
        $validator = Validator::make($req->all(), [
            'photo_url' => 'required|string', // Validate photo URL
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Update task status and photo
        $task->update([
            'status' => 'completed',
            'photo_url' => $req->photo_url, // Store the uploaded photo URL
        ]);

        return response()->json(['message' => 'Task selesai', 'task' => $task], 200);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        
        if (!$task || $task->user_sales_id != auth()->user()->id) {
            return response()->json(['message' => 'Task tidak ditemukan'], 404);
        }

        $task->delete();
        
        return response()->json(['message' => 'Task berhasil dihapus'], 200);
    }
}
