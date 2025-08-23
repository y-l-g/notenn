<?php

namespace App\Policies;

use App\Models\Arrangement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArrangementPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Arrangement $arrangement): bool
    {
        return $user->id === $arrangement->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Arrangement $arrangement): bool
    {
        return $user->id === $arrangement->user_id;
    }
}
