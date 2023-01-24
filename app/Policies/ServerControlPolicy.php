<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ServerControl;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServerControlPolicy
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
    // Может ли пользователь добавлять сервер в контроле серверов
    public function add(User $user, ServerControl $server, int $chapter_id)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь удалить сервер в контроле серверов
    public function delete(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь просмотреть сервер в контроле серверов
    public function view(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
}
