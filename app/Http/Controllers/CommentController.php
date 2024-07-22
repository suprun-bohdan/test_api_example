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

        $task = Task::where('id', $taskId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $comment = $task->comments()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return response()->json($comment, 201);
    }

    public function index($taskId): \Illuminate\Http\JsonResponse
    {
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json("Завдання не знайдено", 404);
        }

        $comments = Comment::where('task_id', $taskId)->get();

        if ($comments->isEmpty()) {
            return response()->json("Коментарів до завдання {$task->title} (ID: {$taskId}) немає", 404);
        } else {
            return response()->json($comments);
        }
    }


    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Комент було видалено успішно']);
    }
}
