<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Chapter;
use App\Models\Topic;
use App\Models\Post;
use App\Models\Comment;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ForumController extends Controller
{
    private string $path_files = 'uploads/forum/';

    public function forum()
    {
        $chapters = Chapter::orderBy('ord')->with(['topics' => function ($query) {
            $query->orderBy('ord');
        }])->get();
        return view('forum', ['chapters' => $chapters]);
    }

    public function forum_topic($id)
    {
        $topic = Topic::where('id', $id)->with(['posts' => function ($query) {
            $query->select('id', 'name', 'topic_id', 'chapter_id', 'size');
            $query->orderBy('ord');
        }])->firstOrFail();

        if(!Auth::user()->can('forum.'.$topic->chapter_id.'.'.$topic->id.'.view'))
        {
            abort(403, 'Unauthorized action.');
        }
        return view('forum/topic', ['topic' => $topic]);
    }

    public function forum_post($id)
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

        if(!Auth::user()->can('forum.'.$post->chapter_id.'.'.$post->topic_id.'.'.$post->id.'.view'))
        {
            abort(403, 'Unauthorized action.');
        }

        return view('forum/post', ['id' => $id, 'post' => $post]);
    }

    public function forum_addpost(Request $request)
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

        if(!Auth::user()->can('forum.'.$post->chapter_id.'.'.$post->topic_id.'.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        $post->save();

        $content = $request->input('editor');
        $find_pattern = '<img src=\"\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        $path_save = $this->path_files . $topic->chapter_id . '/' . $topic->id . '/' . $post->id . '/';
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
        $premission = Permission::findOrCreate('forum.'.$post->chapter_id.'.'.$post->topic_id.'.'.$post->id.'.view');
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate('forum.'.$post->chapter_id.'.'.$post->topic_id.'.'.$post->id.'.*');
        Auth::user()->givePermissionTo($premission);

        return redirect('forum/'.$post->topic_id);
    }

    public function forum_editpost(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'ord' => 'required|digits_between:-999,999',
        ]);

        $post = Post::findOrFail($request->input('id'));

        if(!Auth::user()->can('forum.'.$post->chapter_id.'.'.$post->topic_id.'.'.$post->id.'.edit'))
        {
            abort(403, 'Unauthorized action.');
        }

        $post->name = $request->input('name');
        $post->ord = $request->input('ord');
        $post->save();

        return redirect('forum/'.$post->topic_id);
    }

    public function forum_deletepost(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $post = Post::findOrFail($request->id);

        if(!Auth::user()->can('forum.'.$post->chapter_id.'.'.$post->topic_id.'.'.$post->id.'.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        $topic = Topic::findOrFail($post->topic_id);

        Comment::where('post_id', $post->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter_id . '/' . $topic->id . '/' . $post->id);

        Permission::where('name', 'like', 'forum.'.$post->chapter_id.'.'.$post->topic_id.'.'.$post->id.'%')->delete();

        $post->delete();
        $topic->size -= 1;
        $topic->save();

        return redirect('forum/'.$topic->id);
    }

    public function forum_addcomment(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'editor' => 'required|min:10|max:8192',
        ]);

        $post = Post::findOrFail($request->input('id'));
        $topic = Topic::findOrFail($post->topic_id);

        $comment = new Comment();
        $comment->chapter_id = $post->chapter_id;
        $comment->topic_id = $post->topic_id;
        $comment->post_id = $post->id;
        $comment->content = '';
        $comment->user_id = Auth::id();

        if(!Auth::user()->can('forum.'.$comment->chapter_id.'.'.$comment->topic_id.'.'.$comment->post_id.'.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        $comment->save();

        $content = $request->input('editor');
        $find_pattern = '<img src=\"\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        $path_save = $this->path_files . $topic->chapter_id . '/' . $topic->id . '/' . $post->id . '/' . $comment->id . '/';
        foreach ($matches[1] as $value) {
            Storage::disk('public')->move('temp/' . $value, $path_save . $value);
        }
        $replace_patternt = '/<img src=\"\/storage\/temp\//';
        $content = preg_replace($replace_patternt, '<img class="img-fluid" src="/storage/' . $path_save, $content);


        $comment->content = $content;
        $comment->save();
        $post->size += 1;
        $post->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate('forum.'.$comment->chapter_id.'.'.$comment->topic_id.'.'.$comment->post_id.'.'.$comment->id.'.view');
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate('forum.'.$comment->chapter_id.'.'.$comment->topic_id.'.'.$comment->post_id.'.'.$comment->id.'.*');
        Auth::user()->givePermissionTo($premission);

        return redirect('forum/post/'.$comment->post_id);
    }

    public function forum_deletecomment(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $comment = Comment::findOrFail($request->input('id'));

        if(!Auth::user()->can('forum.'.$comment->chapter_id.'.'.$comment->topic_id.'.'.$comment->post_id.'.'.$comment->id.'.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        $post = Post::findOrFail($comment->post_id);
        $topic = Topic::findOrFail($post->topic_id);

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter_id . '/' . $topic->id . '/' . $post->id . '/' . $comment->id);

        Permission::where('name', 'like', 'forum.'.$comment->chapter_id.'.'.$comment->topic_id.'.'.$comment->post_id.'.'.$comment->id.'%')->delete();

        $comment->delete();
        $post->size -= 1;
        $post->save();

        return redirect('forum/post/'.$post->id);
    }

    public function editor_image_upload(Request $request)
    {
        $file = $request->file('upload');
        $path = $file->store('temp', 'public');
        return ['url' => "/storage/$path"];
    }
}
