@extends('server-management.layout')
@section('title')
    Управление серверами | Игроки
@endsection
@section('secondary_content')
@foreach ($players as $player)
<div class="list-group w-auto">
    <a href="{{$player['profileurl']}}" class="list-group-item list-group-item-action d-flex gap-3 py-3" aria-current="true">
      <img src={{$player['avatar']}} alt="twbs" width="32" height="32" class="rounded-circle flex-shrink-0">
      <div class="d-flex gap-2 w-100 justify-content-between">
        <div>
            <h6 class="mb-0">{{$player['personaname']}}</h6>
          <p class="mb-0 opacity-75">{{$player['steamid']}}</p>
        </div>
        <small class="opacity-50 text-nowrap">{{date("H:i:s", round($player['time']))}}</small>
      </div>
    </a>
</div>
@endforeach
@endsection