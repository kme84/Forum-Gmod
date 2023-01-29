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
    public function add(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь удалить сервер в контроле серверов
    public function delete(User $user, ServerControl $server, int $id)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь просмотреть сервера в контроле серверов
    public function view(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь просмотреть консоль сервера
    public function view_console(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь запускать команды на сервере
    public function run_command(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь запускать луа на сервере
    public function run_lua(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь просмотреть ошибки на сервере
    public function view_errors(User $user, ServerControl $server)
    {
        return $user->role === 'admin';
    }
}
