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
    <button class="mb-4 w-100 btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#exampleModal">Добавить сервер</button>
    @foreach ($servers as $server)
    <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
        <div class="col p-4 d-flex flex-column position-static">
        <strong class="d-inline-block mb-2 text-primary">Online</strong>
        <h3 class="mb-0">{{$server->gamemode}}</h3>
        <div class="mb-1 text-muted">IP: {{$server->ipport}}</div>
        <p class="card-text mb-auto">{{$server->description}}</p>
        <form method="post" action="servers/delete">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$server->id}}">
            <input class="btn btn-danger" type="submit" value="Удалить">
        </form>
        </div>
        <div class="col-auto d-none d-lg-block">
            <img class="img-fluid" width="400" height="250" src="{{asset('/storage/'.$server->banner)}}" alt="Баннер">
        </div>
    </div>
    @endforeach
</div>
<!-- Modal AddServer -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Добавление сервера</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form class="d-grid gap-3" id="addserver" method="post" action="servers/add" enctype="multipart/form-data" onsubmit="return this.addserver.disabled=true;">
                @csrf
                <input type="text" name="ipport" id="ipport" placeholder="IP:PORT" class="form-control">
                <input type="text" name="gamemode" id="gamemode" placeholder="Название режима" class="form-control">
                <label for="banner">Баннер сервера:
                    <input type="file" name="banner" id="banner">
                </label>
                <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="Укажите описание сервера..."></textarea>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            <input class="btn btn-success" name="addserver" type="submit" form="addserver" value="Добавить">
        </div>
        </div>
    </div>
</div>
@endsection