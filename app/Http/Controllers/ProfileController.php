<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\Facades\Image;

use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile($id)
    {
        $user = User::findOrFail($id);
        return view('profile', ['user' => $user]);
    }
    
    public function profile_edit($id)
    {
        $user = User::findOrFail($id);
        return view('profile_edit', ['user' => $user]);
    }
    
    public function profile_edit_public(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'inputUsername' => 'required|min:3|max:25',
            'inputBio' => 'nullable|min:5|max:500',
            'uploadAvatar' => 'nullable|file|mimes:jpg,bmp,png,gif'
        ]);

        $file = $request->file('uploadAvatar');

        $user = User::find($request->input('id'));

        $user->name = $request->input('inputUsername');
        $user->bio = $request->input('inputBio');

        if($file)
        {
            $path = $file->store('uploads', 'public');

            $img = Image::make(Storage::path('/public/').$path)->fit(128, 128)->save(Storage::path('/public/').$path);

            $canvas = Image::canvas(128, 128, '#ffffff');
            $canvas->insert(Storage::path('/public/').$path, 'center')->save(Storage::path('/public/').$path);
            
            if($user->avatar)
            {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $path;
        }

        $user->save();

        return redirect('profile/'.$request->input('id').'/edit');
    }
    public function profile_edit_private(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'inputFirstName' => 'nullable|min:3|max:500',
            'inputLastName' => 'nullable|min:3|max:500',
            'inputState' => 'required|min:1|max:25',
        ]);

        $user = User::find($request->input('id'));

        $user->firstname = $request->input('inputFirstName');
        $user->lastname = $request->input('inputLastName');
        $user->sex = $request->input('inputState');
        $user->save();

        return redirect('profile/'.$request->input('id').'/edit');
    }
    public function profile_edit_password(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required',
            'inputPasswordCurrent' => 'min:8|max:32',
            'inputPasswordNew' => 'required|min:8|max:32',
            'inputPasswordNew2' => 'required|min:8|max:32',
        ]);

        $user = User::find($request->input('id'));

        if(Auth::user()->role === "admin" && Auth::id() != $request->input('id'))
        {
            $this->authorize('edit', $user);
            if ($request->input('inputPasswordNew') == $request->input('inputPasswordNew2')) {
                $user->password = Hash::make($request->input('inputPasswordNew'));
                $user->save();
            }
        }
        else
        {
            if ($request->input('inputPasswordNew') == $request->input('inputPasswordNew2') && Hash::check($request->input('inputPasswordCurrent'), $user->password)) {
                $user->password = Hash::make($request->input('inputPasswordNew'));
                $user->save();
            }
        }

        
        return redirect('profile/'.$request->input('id').'/edit');
    }
}
