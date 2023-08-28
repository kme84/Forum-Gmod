<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Chapter;

class ForumController extends Controller
{
    public function forum()
    {
        $chapters = Chapter::orderBy('ord')->with(['topics' => function ($query) {
            $query->orderBy('ord');
        }])->get();
        return view('forum', ['chapters' => $chapters]);
    }

    public function editor_image_upload(Request $request)
    {
        $file = $request->file('upload');
        $path = $file->store('temp', 'public');
        return ['url' => "/storage/$path"];
    }
}
