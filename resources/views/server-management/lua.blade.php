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
          <a class="nav-link active" href="/server-management/lua/{{$server->id}}">Lua</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/server-management/errors/{{$server->id}}">Ошибки</a>
        </li>
    </ul>
    <form>
        <textarea style="overflow:auto;resize:none" class="w-100" rows="25" wrap="off"></textarea>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary mb-3">Выполнить</button>
        </div>
    </form>
    <textarea style="overflow:auto;resize:none" class="w-100" rows="25" readonly="" wrap="off"></textarea>
</div>
@endsection