<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Chapter;
use App\Models\Topic;
use App\Models\Post;
use App\Models\Comment;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ChapterController extends Controller
{
    private string $path_files = 'uploads/forum';

    public function add(Request $request)
    {
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
        $premission = Permission::findOrCreate("forum.$chapter->id.view");
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate("forum.$chapter->id.*");
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

        $chapter = Chapter::findOrFail($request->input('id'));

       if(!Auth::user()->can("forum.$chapter->id.edit"))
        {
            abort(403, 'Unauthorized action.');
        }

        $chapter->name = $request->input('name');
        $chapter->ord = $request->input('ord');
        $chapter->save();

        return redirect('/control-panel/forum');
    }

    public function delete(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
        ]);

        $chapter = Chapter::findOrFail($request->input('id'));

        if(!Auth::user()->can("forum.$chapter->id.delete"))
        {
            abort(403, 'Unauthorized action.');
        }

        Comment::where('chapter_id', $chapter->id)->delete();
        Post::where('chapter_id', $chapter->id)->delete();
        Topic::where('chapter_id', $chapter->id)->delete();

        Storage::disk('public')->deleteDirectory("$this->path_files/$chapter->id");

        Permission::where('name', 'like', "forum.$chapter->id.%")->delete();

        $chapter->delete();

        return redirect('/control-panel/forum');
    }
}
