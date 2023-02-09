<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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

    public function add(User $user, Post $posts, int $topic_id)
    {
        return $user->id === $posts->author;
    }

    public function delete(User $user, Post $posts)
    {
        return $user->id === $posts->author;
    }
}
