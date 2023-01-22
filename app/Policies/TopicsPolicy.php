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
    // Может ли пользователь добавлять тему в раздел
    public function add(User $user, Topics $topic, int $chapter_id)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь удалить тему
    public function delete(User $user, Topics $topic)
    {
        return $user->role === 'admin';
    }
}
