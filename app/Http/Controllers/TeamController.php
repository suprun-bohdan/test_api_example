<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Requests\TeamRequest;
use App\Http\Requests\TeamUserRequest;
use App\Http\Resources\TeamResource;
use App\Http\Resources\UserResource;
use App\Repositories\TeamRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends Controller
{
    protected TeamRepository $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $teams = $this->teamRepository->findByUserId($request->user()->id);
        return TeamResource::collection($teams);
    }

    public function store(TeamRequest $request): TeamResource
    {
        $team = $this->teamRepository->create($request->validated());
        $team->users()->attach($request->user()->id);
        return new TeamResource($team);
    }

    public function show(Team $team): TeamResource
    {
        return new TeamResource($team);
    }

    public function update(TeamRequest $request, Team $team): TeamResource
    {
        $this->teamRepository->update($team, $request->validated());
        return new TeamResource($team);
    }

    public function destroy(Team $team): JsonResponse
    {
        $this->teamRepository->delete($team);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function addUser(TeamUserRequest $request, Team $team): JsonResponse
    {
        if ($this->teamRepository->userExistsInTeam($team->id, $request->user_id)) {
            return new JsonResponse(['message' => 'User already in the team'], Response::HTTP_CONFLICT);
        }

        $this->teamRepository->addUserToTeam($team->id, $request->user_id);
        $user = $team->users()->find($request->user_id);
        return new JsonResponse(['message' => 'User added to the team', 'user' => new UserResource($user)], Response::HTTP_OK);
    }

    public function removeUser(Team $team, int $userId): JsonResponse
    {
        if (!$team->users()->where('user_id', $userId)->exists()) {
            return new JsonResponse(['message' => 'User not found in the team'], Response::HTTP_NOT_FOUND);
        }

        $team->users()->detach($userId);
        return new JsonResponse(['message' => 'User removed from the team'], Response::HTTP_OK);
    }
}
