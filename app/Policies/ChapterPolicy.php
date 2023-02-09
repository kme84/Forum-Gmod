<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Chapter;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChapterPolicy
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
    
    // Может ли пользователь добавить раздел
    public function add(User $user, Chapter $chapter)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь удалить раздел
    public function delete(User $user, Chapter $chapters)
    {
        return $user->role === 'admin';
    }
}
