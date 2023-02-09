<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
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
    public function add(User $user, Topic $topic, int $chapter_id)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь удалить тему
    public function delete(User $user, Topic $topic)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь просмотреть тему
    public function view(User $user, Topic $topic)
    {
        return $user->role === 'admin';
    }
}
