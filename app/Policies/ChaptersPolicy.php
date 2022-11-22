<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chapters;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChaptersPolicy
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

    public function add(User $user, Chapters $chapters)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Chapters $chapters)
    {
        return $user->role === 'admin';
    }
}
