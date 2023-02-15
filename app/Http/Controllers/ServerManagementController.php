<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\ServerControl;
use App\Models\ServerError;
use App\Models\ServerCommand;
use App\Models\Task;
use App\Models\User;

class ServerManagementController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
    
    public function servermanagement()
    {
        return view('servermanagement', ['servers' => ServerControl::all()]);
    }
    public function servermanagement_add(Request $request)
    {
        $server = new ServerControl();

        $this->authorize('add', $server);

        $valid = $request->validate([
            'ipport' => 'required|min:4|max:100',
            'gamemode' => 'required|min:4|max:100',
        ]);

        $server->ipport = $request->input('ipport');
        $server->gamemode = $request->input('gamemode');
        $server->author = Auth::id();

        $server->save();

        return redirect('server-management');
    }
    public function servermanagement_delete(Request $request)
    {
        $server = ServerControl::findOrFail($request->id);

        $this->authorize('delete', $server);

        $server->delete();

        //ServerControl::destroy($request->id);
        return redirect('server-management');
    }
    public function servermanagement_console($id)
    {
        $server = ServerControl::findOrFail($id);

        $this->authorize('view_console', $server);

        return view('server-management/console', ['server' => $server]);
    }
    public function servermanagement_console_receive(Request $request)
    {
        // $valid = $request->validate([
        //     'port' => 'required|min:3|max:6',
        //     'newfile' => 'required|min:1|max:5',
        //     'log' => 'required',
        // ]);
        $ip = $request->ip();
        $port = $request->port;
        $server = ServerControl::where('ipport', '=', $ip.':'.$port)->firstOrFail();
        Storage::disk('local')->makeDirectory('servercontrol/'.$server->id);
        if ($request->newfile === "true")
        {
            Storage::disk('local')->put('servercontrol/'.$server->id.'/serverlogs.txt', $request->log);
        }
        else
        {
            if ($request->log != "")
            {
                Storage::disk('local')->append('servercontrol/'.$server->id.'/serverlogs.txt', $request->log);
            }
        }

        Storage::disk('local')->put('servercontrol/'.$server->id.'/players.txt', $request->players);
        $errors = json_decode($request->errors, true);
        foreach($errors as $realm=>$error_array)
        {
            foreach ($error_array as $error_stack=>$error_count)
            {
                $error = ServerError::where('server_id', '=', $server->id)->where('error', '=', $error_stack)->first();
                if (!$error)
                {
                    $error = new ServerError();
                    $error->server_id = $server->id;
                    $error->error = $error_stack;
                    $error->count = $error_count;
                    $error->realm = $realm;
                    $error->save();
                }
                else
                {
                    $error->count += $error_count;
                    $error->save();
                }
            }
        }
        
        $commands = ServerCommand::where('server_id', '=', $server->id)->get();

        $commands_array = $commands ? $commands->toArray() : [];

        foreach($commands as $command)
        {
            $command->delete();
        }

        return $commands_array;
    }
    public function servermanagement_console_update(Request $request)
    {
        $valid = $request->validate([
            'id' => 'required|min:1|max:8',
            'tell' => 'required|min:1|max:20',
        ]);
        $id = $request->id;
        if (Storage::disk('local')->missing('servercontrol/'.$id.'/serverlogs.txt'))
        {
            return ['rows' => "", 'tell' => 0];
        }
        $path = Storage::disk('local')->path('servercontrol/'.$id.'/serverlogs.txt');
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
        $arr = mb_convert_encoding(['rows' => $rows, 'tell' => $tell], "UTF-8", "auto");
        return $arr;
    }
    public function servermanagement_console_runcommand(Request $request)
    {

        $valid = $request->validate([
            'id' => 'required',
            'type' => 'required|min:1|max:20',
            'command' => 'required',
        ]);

        $server = ServerControl::findOrFail($request->input('id'));
        if ($request->input('type') == "command")
        {
            $this->authorize('run_command', $server);
        }
        elseif ($request->input('type') == "lua")
        {
            $this->authorize('run_lua', $server);
        }
    
        $command = new ServerCommand();
        $command->server_id = $server->id;
        $command->type = $request->input('type');
        $command->command = $request->input('command');
        $command->save();
        return response('', 200)
        ->header('Content-Type', 'text/plain');
    }
    public function servermanagement_players($id)
    {
        $server = ServerControl::findOrFail($id);

        $this->authorize('view_console', $server);

        if (Storage::disk('local')->exists($id.'/players.txt'))
        {
            $players = Storage::disk('local')->get($id.'/players.txt');
            $players = json_decode($players, true);
            $steamids = '';
            foreach($players as $steamid=>$player)
            {
                $steamids .= $steamid . ',';
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=A86A2797CC650D04D91E9B5F17880143&format=json&steamids=".$steamids);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            $players_full = json_decode($output, true)['response']['players'];
            foreach($players_full as &$player)
            {
                $player = array_merge($player, $players[$player['steamid']]);
            }

            return view('server-management/players', ['server' => $server, 'players' => $players_full]);
        }
        return view('server-management/players', ['server' => $server, 'players' => array()]);
    }
    public function servermanagement_lua($id)
    {
        $server = ServerControl::findOrFail($id);

        $this->authorize('run_lua', $server);

        return view('server-management/lua', ['server' => $server]);
    }
    public function servermanagement_errors($id)
    {
        $server = ServerControl::findOrFail($id);

        $this->authorize('view_errors', $server);

        $errors = ServerError::where('server_id', '=', $server->id)->get();
        return view('server-management/errors', ['server' => $server, 'errors' => $errors]);
    }
    public function servermanagement_error_delete(Request $request)
    {
        $error = ServerError::findOrFail($request->input('id'));
        $error->delete();
        
        return response('', 200)
        ->header('Content-Type', 'text/plain');
    }
    public function servermanagement_task_add(Request $request)
    {
        $valid = $request->validate([
            'server' => 'required',
            'title' => 'required|min:4|max:256',
            'description' => 'required|min:4|max:512',
            'priority' => 'required|digits_between:1,3',
        ]);

        $server = ServerControl::findOrFail($request->input('server'));
        $task = new Task();
        $task->server = $server->id;
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->status = 1;
        $task->author = Auth::id();
        $task->priority = $request->input('priority');
        $task->save();
        
        return redirect('server-management/tasks/'.$server->id);
    }
    public function servermanagement_tasks($id)
    {
        $server = ServerControl::findOrFail($id);

        $this->authorize('view_errors', $server);

        $tasks = Task::where('server', '=', $server->id)->get();
        $priorities = array(1 => 'Низкий', 2 => 'Средний', 3 => 'Высокий');
        return view('server-management/tasks', ['server' => $server, 'tasks' => $tasks, 'users' => User::all(), 'priorities' => $priorities]);
    }
    public function servermanagement_task_delete(Request $request)
    {
        $task = Task::findOrFail($request->input('id'));
        $task->delete();
        
        return response('', 200)
        ->header('Content-Type', 'text/plain');
    }
    public function servermanagement_task_change(Request $request)
    {
        $task = Task::findOrFail($request->input('id'));
        $task->status = $task->status == 2 ? 1 : 2;
        $task->save();
        
        return response('', 200)
        ->header('Content-Type', 'text/plain');
    }
}
