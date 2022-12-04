@extends('layout')
@section('title')
    Управление серверами
@endsection
@section('main_content')
<div class="container">
    <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link" href="/server-management/console/{{$server->id}}">Консоль</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/server-management/players/{{$server->id}}">Игроки</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/server-management/lua/{{$server->id}}">Lua</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/server-management/errors/{{$server->id}}">Ошибки</a>
        </li>
    </ul>
    <div class="list-group list-group-flush border-bottom scrollarea">
      @foreach ($errors as $error)
        @if ($error->realm == 'server')
          <a href="#" class="list-group-item list-group-item-action py-3 lh-tight bg-primary text-white">
            <div class="d-flex w-100 align-items-center justify-content-between">
              <div class="col-10 mb-1 small">{{$error->error}}</div>
              <small class="text-white">x{{$error->count}}</small>
            </div>
          </a>
        @else
          <a href="#" class="list-group-item list-group-item-action py-3 lh-tight bg-warning">
            <div class="d-flex w-100 align-items-center justify-content-between">
              <div class="col-10 mb-1 small">{{$error->error}}</div>
              <small>x{{$error->count}}</small>
            </div>
          </a>
        @endif
      @endforeach
      </div>
</div>
@endsection