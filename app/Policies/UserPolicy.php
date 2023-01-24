<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    // Может ли пользователь добавлять пользователя
    public function add(User $user, User $target)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь удалять пользователя
    public function delete(User $user, User $target)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь просматривать пользователя
    public function view(User $user, User $target)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь редактировать пользователя
    public function edit(User $user, User $target)
    {
        return $user->role === 'admin' || $user->id == $target->id;
    }
}
