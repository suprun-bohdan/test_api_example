<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Task;
use App\Http\Resources\CommentResource;
use App\Repositories\CommentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{
    protected CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function store(CommentRequest $request, Task $task): CommentResource
    {
        $data = [
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'task_id' => $task->id,
        ];
        $comment = $this->commentRepository->create($data);

        return new CommentResource($comment);
    }

    public function index(Task $task): AnonymousResourceCollection
    {
        $comments = $this->commentRepository->findByTaskId($task->id);

        return CommentResource::collection($comments);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $comment = $this->commentRepository->findById($id);
            if (!$comment) {
                throw new ModelNotFoundException();
            }
            $this->commentRepository->delete($comment);
            return response()->json(['message' => 'Comment deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }
    }
}


