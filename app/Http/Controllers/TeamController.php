<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Http\Requests\TeamUserRequest;
use App\Models\Team;
use App\Http\Resources\TeamResource;
use App\Http\Resources\UserResource;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function addUser(TeamUserRequest $request, Team $team): JsonResponse
    {
        if ($this->teamRepository->userExistsInTeam($team->id, $request->user_id)) {
            return response()->json(['message' => 'User already in the team'], Response::HTTP_CONFLICT);
        }

        $this->teamRepository->addUserToTeam($team->id, $request->user_id);
        $user = $team->users()->find($request->user_id);
        return response()->json(['message' => 'User added to the team', 'user' => new UserResource($user)], Response::HTTP_OK);
    }

    public function removeUser(Request $request, Team $team, int $userId): JsonResponse
    {
        if (!$team->users()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'User not found in the team'], Response::HTTP_NOT_FOUND);
        }

        $team->users()->detach($userId);

        return response()->json(['message' => 'User removed from the team'], Response::HTTP_OK);
    }

    public function destroy(Request $request, Team $team): JsonResponse
    {
        try {
            $team = $this->teamRepository->findById($team->id);

            if (!$team) {
                return response()->json(['message' => 'Team not found.'], Response::HTTP_NOT_FOUND);
            }

            if (!$team->users()->where('user_id', $request->user()->id)->exists()) {
                return response()->json(['message' => 'You are not a member of this team.'], Response::HTTP_FORBIDDEN);
            }

            $team->users()->detach();
            $this->teamRepository->delete($team);

            return response()->json(['message' => 'Team and all its users have been deleted successfully.'], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Team not found.'], Response::HTTP_NOT_FOUND);
        }
    }
}
