<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use App\Models\Servers;
use App\Models\Chapters;
use App\Models\Topics;
use App\Models\Posts;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ControlPanelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function statistics()
    {
        return view('control-panel/statistics');
    }
    public function servers()
    {
        $servers = new Servers();
        $this->authorize('view', $servers);
        // if (Gate::denies('view', $servers)) {
        //     abort(403);
        // }
        return view('control-panel/servers', ['servers' => $servers->all()]);
    }
    public function servers_add(Request $request)
    {

        $server = new Servers();

        $this->authorize('add', $server);

        $valid = $request->validate([
            'ipport' => 'required|min:11|max:25',
            'gamemode' => 'required|min:3|max:25',
            'banner' => 'required|file|mimes:jpg,bmp,png,gif',
            'description' => 'required|min:10|max:500'
        ]);

        $file = $request->file('banner');
        $path = $file->store('uploads', 'public');

        $server->ipport = $request->input('ipport');
        $server->gamemode = $request->input('gamemode');
        $server->banner = $path;
        $server->description = $request->input('description');

        $server->save();

        return redirect('control-panel/servers');
    }

    public function servers_delete(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
        ]);

        $server = Servers::findOrFail($request->id);

        $this->authorize('delete', $server);

        Storage::disk('public')->delete($server->banner);
        //unlink(public_path('storage/'.$server->banner));
        $server->delete();

        return redirect('control-panel/servers');
    }

    public function forum_addchapter(Request $request)
    {
        $chapter = new Chapters();

        $this->authorize('add', $chapter);

        $valid = $request->validate([
            'name' => 'required|min:3|max:25',
        ]);

        
        $chapter->name = $request->input('name');

        $chapter->save();

        return redirect('forum');
    }

    public function forum_deletechapter(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
        ]);

        $chapter = Chapters::findOrFail($request->input('id'));
        $this->authorize('delete', $chapter);

        Topics::where('chapter', $request->input('id'))->delete();

        $chapter->delete();

        return redirect('forum');
    }

    public function forum_addtopic(Request $request)
    {
        
        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
        ]);

        $topic = new Topics();
        $this->authorize('add', $topic);

        $topic->chapter = $request->input('id');
        $topic->name = $request->input('name');

        $topic->save();

        return redirect('forum');
    }

    public function forum_deletetopic(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $topic = Topics::findOrFail($request->input('id'));
        $this->authorize('delete', $topic);

        $topic->delete();

        return redirect('forum');
    }

    public function forum_topic($id)
    {
        $topic = Topics::findOrFail($id);
        $posts = Posts::where('topic', $id)->get();
        return view('forum/topic', ['topic' => $topic, 'posts' => $posts]);
    }

    public function forum_post($id)
    {
        $post = Posts::findOrFail($id);
        $author = User::findOrFail($post->author);
        $comments = Comment::where('post', $id)->get();
        $users = array();
        foreach ($comments as $key => $value) {
            $users[$key] = User::findOrFail($value->author);
        }
        return view('forum/post', ['id' => $id, 'post' => $post, 'author' => $author, 'comments' => $comments, 'users' => $users]);
    }

    public function forum_addpost(Request $request)
    {
        //$data = str_replace( '&', '&amp;', $data );
        $post = new Posts();
        $this->authorize('add', $post);

        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'editor' => 'required|min:10|max:1000',
        ]);
        
        $content = $request->input('editor');
        $find_pattern = '<img src=\"https?\:\/\/g-vector\.ru\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        foreach ($matches[1] as $value) {
            Storage::disk('public')->move('temp/' . $value, 'uploads/' . $value);
        }
        $replace_patternt = '/<img src=\"https?\:\/\/g-vector\.ru\/storage\/temp\//';
        $content = preg_replace($replace_patternt, '<img class="img-fluid" src="http://g-vector.ru/storage/uploads/', $content);

        
        $post->topic = $request->input('id');
        $post->title = $request->input('name');
        $post->content = $content;

        $post->save();

        return redirect('forum/'.$post->topic);
    }

    public function forum_deletepost(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $post = Posts::findOrFail($request->id);
        $this->authorize('delete', $post);

        $find_pattern = '<img class="img-fluid" src=\"https?\:\/\/g-vector\.ru\/storage\/(uploads\/[a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $post->content, $matches);

        foreach ($matches[1] as $value) {
            Storage::disk('public')->delete($value);
        }
        $topic = $post->topic;
        $post->delete();

        return redirect('forum/'.$topic);
    }

    public function forum_addcomment(Request $request)
    {
        //$data = str_replace( '&', '&amp;', $data );
        $comment = new Comment();
        $this->authorize('add', $comment);

        $valid = $request->validate([
            'id' => 'required',
            'editor' => 'required|min:10|max:1000',
        ]);
        
        $content = $request->input('editor');
        $find_pattern = '<img src=\"https?\:\/\/g-vector\.ru\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        foreach ($matches[1] as $value) {
            Storage::disk('public')->move('temp/' . $value, 'uploads/' . $value);
        }
        $replace_patternt = '/<img src=\"https?\:\/\/g-vector\.ru\/storage\/temp\//';
        $content = preg_replace($replace_patternt, '<img class="img-fluid" src="http://g-vector.ru/storage/uploads/', $content);

        
        $comment->post = $request->input('id');
        $comment->content = $content;
        $comment->author = Auth::id();

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

        $post_id = $comment->post;

        $comment->delete();

        return redirect('forum/post/'.$post_id);
    }

    public function editor_image_upload(Request $request)
    {
        $file = $request->file('upload');
        $path = $file->store('temp', 'public');
        return json_encode(['url' => asset('storage/'.$path)]);
    }
}
