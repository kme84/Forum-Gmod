<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Server;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerPolicy
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

    public function view(User $user, Server $servers)
    {
        return $user->role === 'admin';
    }

    public function add(User $user, Server $servers)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Server $servers)
    {
        return $user->role === 'admin';
    }
}
