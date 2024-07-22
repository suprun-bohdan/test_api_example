<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use App\Repositories\TaskRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    protected TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tasks = $this->taskRepository->findByUserId($request->user()->id);

        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request): TaskResource
    {
        $task = $this->taskRepository->create($request->validated() + ['user_id' => $request->user()->id]);
        return new TaskResource($task);
    }

    public function show($id): TaskResource
    {
        $task = $this->taskRepository->findById($id);
        return new TaskResource($task);
    }

    public function update(TaskRequest $request, int $id): TaskResource
    {
        $task = $this->taskRepository->findById($id);
        $this->taskRepository->update($task, $request->validated());
        return new TaskResource($task);
    }

    public function destroy($id): JsonResponse
    {
        $task = $this->taskRepository->findById($id);
        $this->taskRepository->delete($task);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
