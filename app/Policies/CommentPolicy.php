<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
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

    public function before($user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    public function add(User $user, Comment $comment)
    {
        return true;
    }

    public function delete(User $user, Comment $comment)
    {
        return $user->id === $comment->author;
    }
}
