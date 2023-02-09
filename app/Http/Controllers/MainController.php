<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Server;

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
        return view('main', ['servers' => Server::all()]);
    }
    
    public function users()
    {
        //$request = json_decode(file_get_contents( "https://randomuser.me/api/?results=20" ));
        return view('users', ['results' => User::all()]);
    }
    
}
