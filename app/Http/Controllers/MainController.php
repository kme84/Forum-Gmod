<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\ServerControl;
use App\Models\Servers;
use App\Models\Chapters;
use App\Models\Topics;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    
    public function main()
    {
        return view('main', ['servers' => Servers::all()]);
    }
    public function forum()
    {
        return view('forum', ['chapters' => Chapters::all(), 'topics' => Topics::all()]);
    }
    public function users()
    {
        //$request = json_decode(file_get_contents( "https://randomuser.me/api/?results=20" ));
        return view('users', ['results' => User::all()]);
    }
    public function controlpanel($option)
    {
        if (view()->exists('control-panel/' . $option))
        {
            return view('control-panel/' . $option);
        }
        else
        {
            return App::abort(404);
        }
    }
    public function servermanagement()
    {
        return view('servermanagement', ['servers' => ServerControl::all()]);
    }
    public function servermanagement_add(Request $request)
    {
        $valid = $request->validate([
            'ipport' => 'required|min:4|max:100',
            'gamemode' => 'required|min:4|max:100',
        ]);

        $review = new ServerControl();
        $review->ipport = $request->input('ipport');
        $review->gamemode = $request->input('gamemode');

        $review->save();

        return redirect('server-management');
    }
    public function servermanagement_delete(Request $request)
    {
        ServerControl::destroy($request->id);
        return redirect('server-management');
    }
    public function servermanagement_console($id)
    {
        $server = ServerControl::findOrFail($id);
        return view('server-management/console', ['server' => $server]);
    }
    public function servermanagement_console_receive(Request $request)
    {
        $newfile = $request->newfile;
        $log = $request->log;
        if ($newfile === "true")
        {
            Storage::disk('local')->put('serverlogs.txt', $log);
        }
        else
        {
            Storage::disk('local')->append('serverlogs.txt', $log);
        }
        return response('received', 200)
        ->header('Content-Type', 'text/plain');
    }
    public function servermanagement_console_update(Request $request)
    {
        $path = Storage::disk('local')->path('serverlogs.txt');
        $fp = fopen($path, "r");
        if ($request->tell > filesize($path))
        {
            fseek($fp, 0);
        }
        else
        {
            fseek($fp, $request->tell);
        }
        $rows = fgets($fp);
        while (!feof($fp)) {
            $rows .= fgets($fp);
        }
        $tell = ftell($fp);
        fclose($fp);
        
        $rows = $rows ? $rows : '';
        return ['rows' => $rows, 'tell' => $tell];
    }
    public function servermanagement_players($id)
    {
        $server = ServerControl::findOrFail($id);
        return view('server-management/players', ['server' => $server, 'results' => User::all()]);
    }
    public function servermanagement_lua($id)
    {
        $server = ServerControl::findOrFail($id);
        return view('server-management/lua', ['server' => $server]);
    }
    public function servermanagement_errors($id)
    {
        $server = ServerControl::findOrFail($id);
        return view('server-management/errors', ['server' => $server]);
    }
    public function profile_edit()
    {
        return view('profile_edit');
    }
    public function profile_edit_public(Request $request)
    {
        $valid = $request->validate([
            'inputUsername' => 'required|min:3|max:25',
            'inputBio' => 'nullable|min:5|max:500',
            'uploadAvatar' => 'nullable|file|mimes:jpg,bmp,png,gif'
        ]);

        $file = $request->file('uploadAvatar');

        $user = User::find(Auth::id());

        $user->name = $request->input('inputUsername');
        $user->bio = $request->input('inputBio');

        if($file)
        {
            $path = $file->store('uploads', 'public');
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = $path;
        }

        $user->save();

        return redirect('profile/'.Auth::id().'/edit');
    }
    public function profile_edit_private(Request $request)
    {
        $valid = $request->validate([
            'inputFirstName' => 'nullable|min:3|max:500',
            'inputLastName' => 'nullable|min:3|max:500',
            'inputState' => 'required|min:1|max:25',
        ]);

        $user = User::find(Auth::id());

        $user->firstname = $request->input('inputFirstName');
        $user->lastname = $request->input('inputLastName');
        $user->sex = $request->input('inputState');
        $user->save();

        return redirect('profile/'.Auth::id().'/edit');
    }
    public function profile_edit_password(Request $request)
    {
        $valid = $request->validate([
            'inputPasswordCurrent' => 'required|min:8|max:32',
            'inputPasswordNew' => 'required|min:8|max:32',
            'inputPasswordNew2' => 'required|min:8|max:32',
        ]);

        $user = User::find(Auth::id());

        if ($request->input('inputPasswordNew') == $request->input('inputPasswordNew2') && Hash::check($request->input('inputPasswordCurrent'), $user->password)) {
            $user->password = Hash::make($request->input('inputPasswordNew'));
            $user->save();
        }
        return redirect('profile/'.Auth::id().'/edit');
    }
    public function profile($id)
    {
        $user = User::findOrFail($id);
        return view('profile', ['user' => $user]);
    }
}
