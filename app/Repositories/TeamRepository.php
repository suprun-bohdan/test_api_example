<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    public function create(array $data): Team
    {
        return Team::create($data);
    }

    public function findById(int $id): ?Team
    {
        return Team::find($id);
    }

    public function findByUserId(int $userId): Collection
    {
        return Team::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();
    }

    public function delete(Team $team): bool
    {
        $team->users()->detach();

        return $team->delete();
    }

    public function userExistsInTeam(int $teamId, int $userId): bool
    {
        $team = Team::find($teamId);
        return $team->users()->where('user_id', $userId)->exists();
    }

    public function addUserToTeam(int $teamId, int $userId): void
    {
        $team = Team::find($teamId);
        $team->users()->attach($userId);
    }

    public function removeAllUsers(Team $team): void
    {
        $team->users()->detach();
    }
}
