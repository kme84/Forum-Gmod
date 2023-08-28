<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Topic;
use App\Models\Post;
use App\Models\Comment;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PostController extends Controller
{
    private string $path_files = 'uploads/forum';

    public function view($id)
    {
        $post = Post::where('posts.id', $id)
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('topics', 'posts.topic_id', '=', 'topics.id')
        ->select('posts.*', 'users.name as author_name', 'users.avatar as author_avatar', 'topics.name as topic_name')
        ->with(['comments' => function ($query) {
            $query->join('users', 'comments.user_id', '=', 'users.id');
            $query->select('comments.*', 'users.name as author_name', 'users.avatar as author_avatar');
        }])
        ->firstOrFail();

        if(!Auth::user()->can("forum.$post->chapter_id.$post->topic_id.$post->id.view"))
        {
            abort(403, 'Unauthorized action.');
        }

        return view('forum/post', ['id' => $id, 'post' => $post]);
    }

    public function add(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'editor' => 'required|min:10|max:16384',
        ]);

        $topic = Topic::findOrFail($request->input('id'));

        $post = new Post();
        $post->chapter_id = $topic->chapter_id;
        $post->topic_id = $topic->id;
        $post->name = $request->input('name');
        $post->content = '';
        $post->user_id = Auth::id();
        $post->ord = 0;
        $post->size = 0;

        if(!Auth::user()->can("forum.$post->chapter_id.$post->topic_id.create"))
        {
            abort(403, 'Unauthorized action.');
        }

        $post->save();

        $content = $request->input('editor');
        $find_pattern = '<img src=\"\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        $path_save = "$this->path_files/$topic->chapter_id/$topic->id/$post->id/";
        foreach ($matches[1] as $value) {
            Storage::disk('public')->move('temp/' . $value, $path_save . $value);
        }
        $replace_patternt = '/<img src=\"\/storage\/temp\//';
        $content = preg_replace($replace_patternt, '<img class="img-fluid" src="/storage/' . $path_save, $content);

        $post->content = $content;
        $post->save();

        $topic->size += 1;
        $topic->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate("forum.$post->chapter_id.$post->topic_id.$post->id.view");
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate("forum.$post->chapter_id.$post->topic_id.$post->id.*");
        Auth::user()->givePermissionTo($premission);

        return redirect("forum/$post->topic_id");
    }

    public function edit(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'ord' => 'required|digits_between:-999,999',
        ]);

        $post = Post::findOrFail($request->input('id'));

        if(!Auth::user()->can("forum.$post->chapter_id.$post->topic_id.$post->id.edit"))
        {
            abort(403, 'Unauthorized action.');
        }

        $post->name = $request->input('name');
        $post->ord = $request->input('ord');
        $post->save();

        return redirect("forum/$post->topic_id");
    }

    public function delete(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $post = Post::findOrFail($request->id);

        if(!Auth::user()->can("forum.$post->chapter_id.$post->topic_id.$post->id.delete"))
        {
            abort(403, 'Unauthorized action.');
        }

        $topic = Topic::findOrFail($post->topic_id);

        Comment::where('post_id', $post->id)->delete();

        Storage::disk('public')->deleteDirectory("$this->path_files/$topic->chapter_id/$topic->id/$post->id");

        Permission::where('name', 'like', "forum.$post->chapter_id.$post->topic_id.$post->id.%")->delete();

        $post->delete();
        $topic->size -= 1;
        $topic->save();

        return redirect("forum/$topic->id");
    }
}
