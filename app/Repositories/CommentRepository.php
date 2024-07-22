<?php
namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class CommentRepository
{
    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function findById(int $id): ?Comment
    {
        return Comment::find($id);
    }

    public function findByTaskId(int $taskId): Collection
    {
        return Comment::where('task_id', $taskId)->get();
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }
}
