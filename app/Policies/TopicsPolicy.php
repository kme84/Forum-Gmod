<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topics;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function add(User $user, Topics $topics)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Topics $topics)
    {
        return $user->role === 'admin';
    }
}
