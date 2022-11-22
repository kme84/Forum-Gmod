<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Servers;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServersPolicy
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

    public function view(User $user, Servers $servers)
    {
        return $user->role === 'admin';
    }

    public function add(User $user, Servers $servers)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Servers $servers)
    {
        return $user->role === 'admin';
    }
}
