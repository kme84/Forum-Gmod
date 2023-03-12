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

    public function forum_addchapter(Request $request)
    {
        //$this->authorize('add', Chapter::class);
        if(!Auth::user()->can('forum.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        $valid = $request->validate([
            'name' => 'required|min:3|max:25',
        ]);

        $chapter = new Chapter();
        $chapter->name = $request->input('name');
        $chapter->ord = 0;
        $chapter->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate('forum.'.$chapter->id.'.view');
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate('forum.'.$chapter->id.'.*');
        Auth::user()->givePermissionTo($premission);

        return redirect('forum');
    }

    public function forum_deletechapter(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
        ]);

        $chapter = Chapter::findOrFail($request->input('id'));

        if(!Auth::user()->can('forum.'.$chapter->id.'.delete'))
        {
            abort(403, 'Unauthorized action.');
        }
        //$this->authorize('delete', $chapter);

        Comment::where('chapter', $chapter->id)->delete();
        Post::where('chapter', $chapter->id)->delete();
        Topic::where('chapter', $chapter->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $chapter->id);

        Permission::where('name', 'like', 'forum.'.$chapter->id.'%')->delete();

        $chapter->delete();

        return redirect('forum');
    }

    public function forum_editchapter(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'ord' => 'required|digits_between:-999,999',
        ]);

        $chapter = Chapter::findOrFail($request->input('id'));
       // $this->authorize('add', $chapter);

       if(!Auth::user()->can('forum.'.$chapter->id.'.edit'))
        {
            abort(403, 'Unauthorized action.');
        }

        $chapter->name = $request->input('name');
        $chapter->ord = $request->input('ord');
        $chapter->save();

        return redirect('forum');
    }

    public function forum_addtopic(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
        ]);

        $topic = new Topic();
        $topic->chapter = $request->input('id');
        $topic->name = $request->input('name');
        $topic->ord = 0;

        //$this->authorize('add', [$topic, $request->input('id')]);
        if(!Auth::user()->can('forum.'.$topic->chapter.'.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        $topic->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate('forum.'.$topic->chapter.'.'.$topic->id.'.view');
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate('forum.'.$topic->chapter.'.'.$topic->id.'.*');
        Auth::user()->givePermissionTo($premission);

        return redirect('forum');
    }

    public function forum_edittopic(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'ord' => 'required|digits_between:-999,999',
        ]);

        $topic = Topic::findOrFail($request->input('id'));
        //$this->authorize('delete', [$topic, $topic->chapter]);

        if(!Auth::user()->can('forum.'.$topic->chapter.'.'.$topic->id.'.edit'))
        {
            abort(403, 'Unauthorized action.');
        }

        $topic->name = $request->input('name');
        $topic->ord = $request->input('ord');
        $topic->save();

        return redirect('forum');
    }

    public function forum_deletetopic(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $topic = Topic::findOrFail($request->input('id'));
        //$this->authorize('delete', [$topic, $topic->chapter]);

        if(!Auth::user()->can('forum.'.$topic->chapter.'.'.$topic->id.'.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        Comment::where('topic', $topic->id)->delete();
        Post::where('topic', $topic->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter . '/' . $topic->id);

        Permission::where('name', 'like', 'forum.'.$topic->chapter.'.'.$topic->id.'%')->delete();

        $topic->delete();

        return redirect('forum');
    }

    public function forum_topic($id)
    {
        $topic = Topic::findOrFail($id);

        if(!Auth::user()->can('forum.'.$topic->chapter.'.'.$topic->id.'.view'))
        {
            abort(403, 'Unauthorized action.');
        }

        $posts = Post::where('topic', $id)->orderBy('ord')->get();
        return view('forum/topic', ['topic' => $topic, 'posts' => $posts]);
    }

    public function forum_post($id)
    {

        $post = Post::where('posts.id', $id)
        ->join('users', 'posts.author', '=', 'users.id')
        ->join('topics', 'posts.topic', '=', 'topics.id')
        ->select('posts.*', 'users.name as author_name', 'users.avatar as author_avatar', 'topics.name as topic_name')
        ->firstOrFail();

        if(!Auth::user()->can('forum.'.$post->chapter.'.'.$post->topic.'.'.$post->id.'.view'))
        {
            abort(403, 'Unauthorized action.');
        }

        $comments = Comment::where('post', $id)
        ->join('users', 'comments.author', '=', 'users.id')
        ->select('comments.*', 'users.name as author_name', 'users.avatar as author_avatar')
        ->get();

        return view('forum/post', ['id' => $id, 'post' => $post, 'comments' => $comments]);
    }

    public function forum_addpost(Request $request)
    {

        //$this->authorize('add', $post);

        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'editor' => 'required|min:10|max:16384',
        ]);

        $topic = Topic::findOrFail($request->input('id'));

        $post = new Post();
        $post->chapter = $topic->chapter;
        $post->topic = $topic->id;
        $post->title = $request->input('name');
        $post->content = '';
        $post->author = Auth::id();
        $post->ord = 0;

        if(!Auth::user()->can('forum.'.$post->chapter.'.'.$post->topic.'.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        $post->save();

        $content = $request->input('editor');
        $find_pattern = '<img src=\"\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        $path_save = $this->path_files . $topic->chapter . '/' . $topic->id . '/' . $post->id . '/';
        foreach ($matches[1] as $value) {
            Storage::disk('public')->move('temp/' . $value, $path_save . $value);
        }
        $replace_patternt = '/<img src=\"\/storage\/temp\//';
        $content = preg_replace($replace_patternt, '<img class="img-fluid" src="/storage/' . $path_save, $content);

        $post->content = $content;
        $post->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate('forum.'.$post->chapter.'.'.$post->topic.'.'.$post->id.'.view');
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate('forum.'.$post->chapter.'.'.$post->topic.'.'.$post->id.'.*');
        Auth::user()->givePermissionTo($premission);

        return redirect('forum/'.$post->topic);
    }

    public function forum_editpost(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'ord' => 'required|digits_between:-999,999',
        ]);

        $post = Post::findOrFail($request->input('id'));
        //$this->authorize('delete', $post);

        if(!Auth::user()->can('forum.'.$post->chapter.'.'.$post->topic.'.'.$post->id.'.edit'))
        {
            abort(403, 'Unauthorized action.');
        }

        $post->title = $request->input('name');
        $post->ord = $request->input('ord');
        $post->save();

        return redirect('forum/'.$post->topic);
    }

    public function forum_deletepost(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $post = Post::findOrFail($request->id);
        //$this->authorize('delete', $post);

        if(!Auth::user()->can('forum.'.$post->chapter.'.'.$post->topic.'.'.$post->id.'.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        $topic = Topic::findOrFail($post->topic);

        Comment::where('post', $post->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter . '/' . $topic->id . '/' . $post->id);

        Permission::where('name', 'like', 'forum.'.$post->chapter.'.'.$post->topic.'.'.$post->id.'%')->delete();

        $post->delete();

        return redirect('forum/'.$topic->id);
    }

    public function forum_addcomment(Request $request)
    {

        //$this->authorize('add', $comment);

        $valid = $request->validate([
            'id' => 'required',
            'editor' => 'required|min:10|max:8192',
        ]);

        $post = Post::findOrFail($request->input('id'));
        $topic = Topic::findOrFail($post->topic);

        $comment = new Comment();
        $comment->chapter = $post->chapter;
        $comment->topic = $post->topic;
        $comment->post = $post->id;
        $comment->content = '';
        $comment->author = Auth::id();

        if(!Auth::user()->can('forum.'.$comment->chapter.'.'.$comment->topic.'.'.$comment->post.'.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        $comment->save();

        $content = $request->input('editor');
        $find_pattern = '<img src=\"\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        $path_save = $this->path_files . $topic->chapter . '/' . $topic->id . '/' . $post->id . '/' . $comment->id . '/';
        foreach ($matches[1] as $value) {
            Storage::disk('public')->move('temp/' . $value, $path_save . $value);
        }
        $replace_patternt = '/<img src=\"\/storage\/temp\//';
        $content = preg_replace($replace_patternt, '<img class="img-fluid" src="/storage/' . $path_save, $content);


        $comment->content = $content;
        $comment->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate('forum.'.$comment->chapter.'.'.$comment->topic.'.'.$comment->post.'.'.$comment->id.'.view');
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate('forum.'.$comment->chapter.'.'.$comment->topic.'.'.$comment->post.'.'.$comment->id.'.*');
        Auth::user()->givePermissionTo($premission);

        return redirect('forum/post/'.$comment->post);
    }

    public function forum_deletecomment(Request $request)
    {
        //$this->authorize('delete', $comment);

        $valid = $request->validate([
            'id' => 'required',
        ]);

        $comment = Comment::findOrFail($request->input('id'));

        if(!Auth::user()->can('forum.'.$comment->chapter.'.'.$comment->topic.'.'.$comment->post.'.'.$comment->id.'.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        $post = Post::findOrFail($comment->post);
        $topic = Topic::findOrFail($post->topic);

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter . '/' . $topic->id . '/' . $post->id . '/' . $comment->id);

        Permission::where('name', 'like', 'forum.'.$comment->chapter.'.'.$comment->topic.'.'.$comment->post.'.'.$comment->id.'%')->delete();

        $comment->delete();

        return redirect('forum/post/'.$post->id);
    }

    public function editor_image_upload(Request $request)
    {
        $file = $request->file('upload');
        $path = $file->store('temp', 'public');
        return ['url' => '/storage/'.$path];
    }
}
