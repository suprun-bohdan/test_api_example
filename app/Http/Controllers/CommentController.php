<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $taskId): \Illuminate\Http\JsonResponse
    {
        $request->validate(['content' => 'required|string']);

        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);

        $comment = $task->comments()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return response()->json($comment, 201);
    }

    public function index($taskId): \Illuminate\Http\JsonResponse
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        return response()->json($task->comments);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
