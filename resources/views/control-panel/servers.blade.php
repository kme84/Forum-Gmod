@extends('control-panel.layout')
@section('title')
    Панель управления | Сервера
@endsection
@section('secondary_content')
<div class="col-md-9 col-lg-8 px-md-4">
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
    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
        <strong class="d-inline-block mb-2 text-primary">Online</strong>
        <h3 class="mb-0">{{$server->gamemode}}</h3>
        <div class="mb-1 text-muted">IP: {{$server->ipport}}</div>
        <p class="card-text mb-auto">{{$server->description}}</p>
        <div class="d-flex">
            <form method="post" action="servers/delete">
                @csrf
                <input type="hidden" name="id" id="id" value="{{$server->id}}">
                <input class="btn btn-danger" type="submit" value="Удалить">
            </form>
            <button class="btn btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#EditServer" onclick="editserver.id.value={{$server->id}};editserver.ipport.value='{{$server->ipport}}';editserver.gamemode.value='{{$server->gamemode}}';editserver.description.value='{{$server->description}}'">Изменить</button>
        </div>
        </div>
        <div class="col-auto d-none d-lg-block">
            <img class="img-fluid" width="400" height="250" src="{{asset('/storage/'.$server->banner)}}" alt="Баннер">
        </div>
    </div>
    @endforeach
</div>

<x-modal modal-id='AddServer' form-id='addserver' action='servers/add' method='post' enctype="multipart/form-data">
    <x-slot:title>
        Добавление сервера
    </x-slot>
    <input type="text" name="ipport" id="ipport" placeholder="IP:PORT" class="form-control">
    <input type="text" name="gamemode" id="gamemode" placeholder="Название режима" class="form-control">
    <label for="banner">Баннер сервера:
        <input type="file" name="banner" id="banner">
    </label>
    <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="Укажите описание сервера..."></textarea>
    <x-slot:button class='btn-success'>
        Добавить
    </x-slot>
</x-modal>

<x-modal modal-id='EditServer' form-id='editserver' action='servers/edit' method='post' enctype="multipart/form-data">
    <x-slot:title>
        Изменение сервера
    </x-slot>
    <input type="hidden" name="id" id="id">
    <input type="text" name="ipport" id="ipport" placeholder="IP:PORT" class="form-control">
    <input type="text" name="gamemode" id="gamemode" placeholder="Название режима" class="form-control">
    <label for="banner">Баннер сервера:
        <input type="file" name="banner" id="banner">
    </label>
    <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="Укажите описание сервера..."></textarea>
    <x-slot:button class='btn-success'>
        Изменить
    </x-slot>
</x-modal>
@endsection