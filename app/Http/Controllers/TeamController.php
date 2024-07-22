<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        $team = Team::create(['name' => $request->name]);

        $team->users()->attach(Auth::id());

        return response()->json($team, 201);
    }

    public function index()
    {
        if (isset(Auth::user()->teams)) {
            return Auth::user()->teams;
        }
    }

    public function addUser(Request $request, $teamId): \Illuminate\Http\JsonResponse
    {
        $team = Team::findOrFail($teamId);

        $request->validate(['user_id' => 'required|exists:users,id']);

        if ($team->users()->where('user_id', $request->user_id)->exists()) {
            return response()->json(['message' => "Ви уже в команді ID: {$teamId}"], 400);
        }

        $team->users()->attach($request->user_id);

        return response()->json(['message' => 'Користувача було додано до команди.']);
    }


    public function removeUser($teamId, $userId): \Illuminate\Http\JsonResponse
    {
        $team = Team::findOrFail($teamId);

        $team->users()->detach($userId);

        return response()->json(['message' => 'Корситувача було видалено з команди.']);
    }
}
