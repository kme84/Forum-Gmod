<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Posts;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostsPolicy
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

    public function add(User $user, Posts $posts, int $topic_id)
    {
        return $user->id === $posts->author;
    }

    public function delete(User $user, Posts $posts)
    {
        return $user->id === $posts->author;
    }
}
