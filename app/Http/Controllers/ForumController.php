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


class ForumController extends Controller
{
    private string $path_files = 'uploads/forum/';

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function forum()
    {
        return view('forum', ['chapters' => Chapter::all(), 'topics' => Topic::all()]);
    }

    public function forum_addchapter(Request $request)
    {
        $chapter = new Chapter();

        $this->authorize('add', $chapter);

        $valid = $request->validate([
            'name' => 'required|min:3|max:25',
        ]);

        $chapter->name = $request->input('name');
        $chapter->ord = 0;
        $chapter->save();

        return redirect('forum');
    }

    public function forum_deletechapter(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
        ]);

        $chapter = Chapter::findOrFail($request->input('id'));
        $this->authorize('delete', $chapter);

        Comment::where('chapter', $chapter->id)->delete();
        Post::where('chapter', $chapter->id)->delete();
        Topic::where('chapter', $chapter->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $chapter->id);

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
        $this->authorize('add', $chapter);

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
        $this->authorize('add', [$topic, $request->input('id')]);

        $topic->chapter = $request->input('id');
        $topic->name = $request->input('name');
        $topic->ord = 0;

        $topic->save();

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
        $this->authorize('delete', [$topic, $topic->chapter]);

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
        $this->authorize('delete', [$topic, $topic->chapter]);

        Comment::where('topic', $topic->id)->delete();
        Post::where('topic', $topic->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter . '/' . $topic->id);

        $topic->delete();

        return redirect('forum');
    }

    public function forum_topic($id)
    {
        $topic = Topic::findOrFail($id);
        $posts = Post::where('topic', $id)->get();
        return view('forum/topic', ['topic' => $topic, 'posts' => $posts]);
    }

    public function forum_post($id)
    {
        $post = Post::findOrFail($id);
        $topic = Topic::findOrFail($post->topic);
        $author = User::findOrFail($post->author);
        $comments = Comment::where('post', $id)->get();
        $users = array();
        foreach ($comments as $key => $value) {
            $users[$key] = User::findOrFail($value->author);
        }
        return view('forum/post', ['id' => $id, 'topic' => $topic, 'post' => $post, 'author' => $author, 'comments' => $comments, 'users' => $users]);
    }

    public function forum_addpost(Request $request)
    {
        $post = new Post();
        $this->authorize('add', $post);

        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'editor' => 'required|min:10|max:16384',
        ]);

        $topic = Topic::findOrFail($request->input('id'));

        $post->chapter = $topic->chapter;
        $post->topic = $topic->id;
        $post->title = $request->input('name');
        $post->content = '';
        $post->author = Auth::id();
        $post->ord = 0;
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
        $this->authorize('delete', $post);

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
        $this->authorize('delete', $post);

        $topic = Topic::findOrFail($post->topic);

        Comment::where('post', $post->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter . '/' . $topic->id . '/' . $post->id);

        $post->delete();

        return redirect('forum/'.$topic->id);
    }

    public function forum_addcomment(Request $request)
    {
        $comment = new Comment();
        $this->authorize('add', $comment);

        $valid = $request->validate([
            'id' => 'required',
            'editor' => 'required|min:10|max:8192',
        ]);

        $post = Post::findOrFail($request->input('id'));
        $topic = Topic::findOrFail($post->topic);

        $comment->chapter = $post->chapter;
        $comment->topic = $post->topic;
        $comment->post = $post->id;
        $comment->content = '';
        $comment->author = Auth::id();
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

        return redirect('forum/post/'.$comment->post);
    }

    public function forum_deletecomment(Request $request)
    {
        $comment = new Comment();
        $this->authorize('delete', $comment);

        $valid = $request->validate([
            'id' => 'required',
        ]);

        $comment = Comment::findOrFail($request->input('id'));
        $post = Post::findOrFail($comment->post);
        $topic = Topic::findOrFail($post->topic);

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter . '/' . $topic->id . '/' . $post->id . '/' . $comment->id);

        $comment->delete();

        return redirect('forum/post/'.$post->id);
    }

    public function editor_image_upload(Request $request)
    {
        $file = $request->file('upload');
        $path = $file->store('temp', 'public');
        return json_encode(['url' => '/storage/'.$path]);
    }
}