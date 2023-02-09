<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\Facades\Image;

use App\Models\Server;


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

    // public function controlpanel($option)
    // {
    //     if (view()->exists('control-panel/' . $option))
    //     {
    //         return view('control-panel/' . $option);
    //     }
    //     else
    //     {
    //         return App::abort(404);
    //     }
    // }

    public function statistics()
    {
        return view('control-panel/statistics');
    }
    public function servers()
    {
        $servers = new Server();
        $this->authorize('view', $servers);
        // if (Gate::denies('view', $servers)) {
        //     abort(403);
        // }
        return view('control-panel/servers', ['servers' => $servers->all()]);
    }
    public function servers_add(Request $request)
    {

        $server = new Server();

        $this->authorize('add', $server);

        $valid = $request->validate([
            'ipport' => 'required|min:11|max:25',
            'gamemode' => 'required|min:3|max:25',
            'banner' => 'required|file|mimes:jpg,bmp,png,gif',
            'description' => 'required|min:10|max:500'
        ]);

        $file = $request->file('banner');
        $path = $file->store('uploads', 'public');
        $img = Image::make(Storage::path('/public/').$path)->fit(512, 512)->save(Storage::path('/public/').$path);

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

        $server = Server::findOrFail($request->id);

        $this->authorize('delete', $server);

        Storage::disk('public')->delete($server->banner);
        //unlink(public_path('storage/'.$server->banner));
        $server->delete();

        return redirect('control-panel/servers');
    }
    
}
