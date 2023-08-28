<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;
use App\Models\Comment;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CommentController extends Controller
{
    private string $path_files = 'uploads/forum';

    public function add(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'editor' => 'required|min:10|max:8192',
        ]);

        $post = Post::findOrFail($request->input('id'));

        $comment = new Comment();
        $comment->chapter_id = $post->chapter_id;
        $comment->topic_id = $post->topic_id;
        $comment->post_id = $post->id;
        $comment->content = '';
        $comment->user_id = Auth::id();

        if(!Auth::user()->can("forum.$comment->chapter_id.$comment->topic_id.$comment->post_id.create"))
        {
            abort(403, 'Unauthorized action.');
        }

        $comment->save();

        $content = $request->input('editor');
        $find_pattern = '<img src=\"\/storage\/temp\/([a-zA-Z0-9]+\.[a-zA-Z0-9]+)\">';
        preg_match_all($find_pattern, $content, $matches);

        $path_save = "$this->path_files/$comment->chapter_id/$comment->topic_id/$comment->post_id/$comment->id/";
        foreach ($matches[1] as $value) {
            Storage::disk('public')->move("temp/$value", $path_save . $value);
        }
        $replace_patternt = '/<img src=\"\/storage\/temp\//';
        $content = preg_replace($replace_patternt, '<img class="img-fluid" src="/storage/' . $path_save, $content);


        $comment->content = $content;
        $comment->save();
        $post->size += 1;
        $post->save();

        $role = Role::findOrCreate('user');
        $premission = Permission::findOrCreate("forum.$comment->chapter_id.$comment->topic_id.$comment->post_id.$comment->id.view");
        $role->givePermissionTo($premission);
        $premission = Permission::findOrCreate("forum.$comment->chapter_id.$comment->topic_id.$comment->post_id.$comment->id.*");
        Auth::user()->givePermissionTo($premission);

        return redirect("forum/post/$comment->post_id");
    }

    public function delete(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
        ]);

        $comment = Comment::findOrFail($request->input('id'));

        if(!Auth::user()->can("forum.$comment->chapter_id.$comment->topic_id.$comment->post_id.$comment->id.delete"))
        {
            abort(403, 'Unauthorized action.');
        }

        $post = Post::findOrFail($comment->post_id);

        Storage::disk('public')->deleteDirectory("$this->path_files/$comment->chapter_id/$comment->topic_id/$comment->post_id/$comment->id/");

        Permission::where('name', 'like', "forum.$comment->chapter_id.$comment->topic_id.$comment->post_id.$comment->id.%")->delete();

        $comment->delete();
        $post->size -= 1;
        $post->save();

        return redirect("forum/post/$post->id");
    }
}
