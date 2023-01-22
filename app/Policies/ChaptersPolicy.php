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
    
    // Может ли пользователь добавить раздел
    public function add(User $user, Chapters $chapter)
    {
        return $user->role === 'admin';
    }
    // Может ли пользователь удалить раздел
    public function delete(User $user, Chapters $chapters)
    {
        return $user->role === 'admin';
    }
}
