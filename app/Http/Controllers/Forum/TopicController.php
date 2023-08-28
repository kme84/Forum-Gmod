<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Topic;
use App\Models\Post;
use App\Models\Comment;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TopicController extends Controller
{
    private string $path_files = 'uploads/forum/';

    public function view($id)
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

    public function add(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
        ]);

        $topic = new Topic();
        $topic->chapter_id = $request->input('id');
        $topic->name = $request->input('name');
        $topic->ord = 0;
        $topic->size = 0;

        if(!Auth::user()->can('forum.'.$topic->chapter_id.'.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        $topic->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate('forum.'.$topic->chapter_id.'.'.$topic->id.'.view');
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate('forum.'.$topic->chapter_id.'.'.$topic->id.'.*');
        Auth::user()->givePermissionTo($premission);

        return redirect('/control-panel/forum');
    }

    public function edit(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'name' => 'required|min:3|max:25',
            'ord' => 'required|digits_between:-999,999',
        ]);

        $topic = Topic::findOrFail($request->input('id'));

        if(!Auth::user()->can('forum.'.$topic->chapter_id.'.'.$topic->id.'.edit'))
        {
            abort(403, 'Unauthorized action.');
        }

        $topic->name = $request->input('name');
        $topic->ord = $request->input('ord');
        $topic->save();

        return redirect('/control-panel/forum');
    }

    public function delete(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $topic = Topic::findOrFail($request->input('id'));

        if(!Auth::user()->can('forum.'.$topic->chapter_id.'.'.$topic->id.'.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        Comment::where('topic_id', $topic->id)->delete();
        Post::where('topic_id', $topic->id)->delete();

        Storage::disk('public')->deleteDirectory($this->path_files . $topic->chapter_id . '/' . $topic->id);

        Permission::where('name', 'like', 'forum.'.$topic->chapter_id.'.'.$topic->id.'%')->delete();

        $topic->delete();

        return redirect('/control-panel/forum');
    }
}
