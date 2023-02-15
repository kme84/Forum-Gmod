@extends('layout')
@section('title')
    Управление серверами
@endsection
@section('main_content')
<div class="container">
    <ul class="nav nav-tabs mb-2">
        <li class="nav-item">
          <a class="nav-link {{Request::is('server-management/console/'.$server->id) ? "active" : "" }}" href="/server-management/console/{{$server->id}}">Консоль</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{Request::is('server-management/players/'.$server->id) ? "active" : "" }}" href="/server-management/players/{{$server->id}}">Игроки</a>
        </li>
        @can('run_lua', $server)
        <li class="nav-item">
          <a class="nav-link {{Request::is('server-management/lua/'.$server->id) ? "active" : "" }}" href="/server-management/lua/{{$server->id}}">Lua</a>
        </li>
        @endcan
        @can('view_errors', $server)
        <li class="nav-item">
          <a class="nav-link {{Request::is('server-management/errors/'.$server->id) ? "active" : "" }}" href="/server-management/errors/{{$server->id}}">Ошибки</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link {{Request::is('server-management/tasks/'.$server->id) ? "active" : "" }}" href="/server-management/tasks/{{$server->id}}">Задачи</a>
        </li>
    </ul>
    @yield('secondary_content')
</div>
@endsection