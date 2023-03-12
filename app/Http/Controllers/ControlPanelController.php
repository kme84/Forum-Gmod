<?php

namespace App\Http\Controllers;

use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\Facades\Image;

use App\Models\Server;

use App\Models\Chapter;
use App\Models\Topic;
use App\Models\Post;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class ControlPanelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function statistics()
    {
        if (!Auth::user()->can('controlpanel.statistics.view'))
        {
            abort(403, 'Unauthorized action.');
        }
        return view('control-panel/statistics');
    }
    public function servers()
    {
        // $servers = new Server();
        // $this->authorize('view', $servers);

        if (!Auth::user()->can('controlpanel.servers.view'))
        {
            abort(403, 'Unauthorized action.');
        }

        return view('control-panel/servers', ['servers' => Server::all()]);
    }
    public function servers_add(Request $request)
    {
        if (!Auth::user()->can('controlpanel.servers.create'))
        {
            abort(403, 'Unauthorized action.');
        }

        //$this->authorize('add', $server);

        $valid = $request->validate([
            'ipport' => 'required|min:11|max:25',
            'gamemode' => 'required|min:3|max:25',
            'banner' => 'required|file|mimes:jpg,bmp,png,gif',
            'description' => 'required|min:10|max:500'
        ]);

        $server = new Server();
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

    public function servers_edit(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'ipport' => 'required|min:11|max:25',
            'gamemode' => 'required|min:3|max:25',
            'banner' => 'required|file|mimes:jpg,bmp,png,gif',
            'description' => 'required|min:10|max:500'
        ]);

        $server = Server::findOrFail($request->input('id'));

        //$this->authorize('delete', $server);
        if (!Auth::user()->can('controlpanel.servers.edit'))
        {
            abort(403, 'Unauthorized action.');
        }

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

        //$this->authorize('delete', $server);
        if (!Auth::user()->can('controlpanel.servers.delete'))
        {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($server->banner);
        //unlink(public_path('storage/'.$server->banner));
        $server->delete();

        return redirect('control-panel/servers');
    }

    public function permissions()
    {
        if (!Auth::user()->can('controlpanel.permissions.view'))
        {
            abort(403, 'Unauthorized action.');
        }

        $chapters = Chapter::all();
        $topics = Topic::all();
        $posts = Post::all();
        $roles = Role::with('permissions')->get();
        return view('control-panel/permissions', ['chapters' => $chapters, 'topics' => $topics, 'posts' => $posts, 'roles' => $roles]);
    }

    public function permissions_role_edit(Request $request)
    {
        if (!Auth::user()->can('controlpanel.permissions.edit'))
        {
            abort(403, 'Unauthorized action.');
        }

        $valid = $request->validate([
            'permissionType' => 'required',
            'role' => 'required|integer',
        ]);

        $role = Role::findOrfail($request->input('role'));

        $permissionType = $request->input('permissionType');
        if ($permissionType == 'forum') {
            $permissionChapter = $request->input('permissionChapter');
            if ($permissionChapter) {
                $permissionType .= '.'.$permissionChapter;
                $permissionTopic = $request->input('permissionTopic');
                if ($permissionTopic) {
                    $permissionType .= '.'.$permissionTopic;
                    $permissionPost = $request->input('permissionPost');
                    if ($permissionPost) {
                        $permissionType .= '.'.$permissionPost;
                    }
                }
            }
        }
        if ($request->input('all')) {
            $premission = Permission::findOrCreate($permissionType.'.*');
            $role->givePermissionTo($premission);
        }
        else
        {
            $premission = Permission::findOrCreate($permissionType.'.*');
            $role->revokePermissionTo($premission);
        }
        if ($request->input('view')) {
            $premission = Permission::findOrCreate($permissionType.'.view');
            $role->givePermissionTo($premission);
        }
        else
        {
            $premission = Permission::findOrCreate($permissionType.'.view');
            $role->revokePermissionTo($premission);
        }
        if ($request->input('create')) {
            $premission = Permission::findOrCreate($permissionType.'.create');
            $role->givePermissionTo($premission);
        }
        else
        {
            $premission = Permission::findOrCreate($permissionType.'.create');
            $role->revokePermissionTo($premission);
        }
        if ($request->input('edit')) {
            $premission = Permission::findOrCreate($permissionType.'.edit');
            $role->givePermissionTo($premission);
        }
        else
        {
            $premission = Permission::findOrCreate($permissionType.'.edit');
            $role->revokePermissionTo($premission);
        }
        if ($request->input('delete')) {
            $premission = Permission::findOrCreate($permissionType.'.delete');
            $role->givePermissionTo($premission);
        }
        else
        {
            $premission = Permission::findOrCreate($permissionType.'.delete');
            $role->revokePermissionTo($premission);
        }

        return redirect('control-panel/permissions');
    }

}
