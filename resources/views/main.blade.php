@extends('layout')
@section('title')
    Главная
@endsection
@section('main_content')
<div class="container mt-5">
  @foreach ($servers as $key => $server)
    <div class="row featurette">
      <div class="col-md-7 order-md-{{($key & 1) ? "1" : "2"}}">
        @php
            $gamemode_a = explode(' ', $server->gamemode);
        @endphp
        <h2 class="featurette-heading">{{$gamemode_a[0]}} 
          @isset($gamemode_a[1])
            <span class="text-muted">{{$gamemode_a[1]}}</span>
          @endisset
        </h2>
        <p class="lead">{{$server->description}}</p>
      </div>
      <div class="col-md-5 order-md-{{!($key & 1) ? "1" : "2"}}">
        <img class="featurette-image img-fluid mx-auto" width="500" height="500" src="{{asset('/storage/'.$server->banner)}}" alt="Баннер">
      </div>
    </div>
    <hr class="featurette-divider">
  @endforeach
</div>
@endsection