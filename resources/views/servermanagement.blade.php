@extends('layout')
@section('title')
    Управление серверами
@endsection
@section('main_content')
<div class="container">
    <h1>Управление серверами</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button class="mb-4 w-100 btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#AddServer">Добавить сервер</button>
    @foreach ($servers as $server)
    <div class="row border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
        <strong class="d-inline-block mb-2 text-primary">Online</strong>
        <h3 class="mb-0">{{$server->gamemode}}</h3>
        </div>
        <div class="col-auto m-auto text-muted">IP: {{$server->ipport}}</div>
        <button class="col-auto m-auto me-2 btn btn-primary" data-bs-toggle="modal" data-bs-target="#EditServer" onclick="editserver.id.value={{$server->id}};editserver.ipport.value='{{$server->ipport}}';editserver.gamemode.value='{{$server->gamemode}}';">Изменить</button>
        <a class="col-auto m-auto btn btn-primary" href="server-management/console/{{$server->id}}">Управление</a>
        <form class="col-auto m-auto" method="post" action="server-management/delete">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$server->id}}">
            <input class="btn btn-danger" type="submit" value="Удалить">
        </form>
    </div>
    @endforeach
</div>

<x-modal modal-id='AddServer' form-id='addserver' action='server-management/add' method='post'>
    <x-slot:title>
        Добавление сервера
    </x-slot>
    <input type="text" name="ipport" id="ipport" placeholder="IP:PORT" class="form-control">
    <input type="text" name="gamemode" id="gamemode" placeholder="Название режима" class="form-control">
    <x-slot:button class='btn-success'>
        Добавить
    </x-slot>
</x-modal>

<x-modal modal-id='EditServer' form-id='editserver' action='server-management/edit' method='post'>
    <x-slot:title>
        Изменение сервера
    </x-slot>
    <input type="hidden" name="id" id="id">
    <input type="text" name="ipport" id="ipport" placeholder="IP:PORT" class="form-control">
    <input type="text" name="gamemode" id="gamemode" placeholder="Название режима" class="form-control">
    <x-slot:button class='btn-success'>
        Изменить
    </x-slot>
</x-modal>
@endsection
