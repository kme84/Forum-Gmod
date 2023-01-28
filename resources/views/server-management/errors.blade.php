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
    <div class="row mt-2">
      <div class="col-md-5 col-xl-4">

          <div class="card">
              <div class="card-header">
                  <h5 class="card-title mb-0">Сторона ошибок</h5>
              </div>

              <div class="list-group list-group-flush" role="tablist">
                  <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#server" role="tab">
                    Сервер
                  </a>
                  <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#client" role="tab">
                    Клиент
                  </a>
              </div>
          </div>
      </div>
      <div class="col-md-7 col-xl-8">
        <div class="tab-content">
          <div class="tab-pane fade show active" id="server" role="tabpanel">
            <div class="list-group list-group-flush border-bottom scrollarea">
              @foreach ($errors as $error)
                @if ($error->realm == 'server')
                  <a href="#" class="list-group-item list-group-item-action py-3 lh-tight bg-primary text-white" onclick="copytoclipboard(this);">
                    <span class="badge rounded-pill bg-secondary mb-1">Создано: {{$error->created_at}}</span>
                    <span class="badge rounded-pill bg-secondary mb-1">Обновлено: {{$error->updated_at}}</span>
                    <button type="button" class="btn-close float-end" aria-label="Close" value="{{$error->id}}" onclick="errordelete(this);"></button>
                    <div class="d-flex w-100 align-items-center justify-content-between">
                      <div class="col-10 mb-1 small">{{$error->error}}</div>
                      <span class="badge rounded-pill bg-secondary mb-1">x{{$error->count}}</span>
                    </div>
                  </a>
                @endif
              @endforeach
            </div>
          </div>
          <div class="tab-pane fade" id="client" role="tabpanel">
            <div class="list-group list-group-flush border-bottom scrollarea">
              @foreach ($errors as $error)
                @if ($error->realm == 'client')
                  <a href="#" class="list-group-item list-group-item-action py-3 lh-tight bg-warning" onclick="copytoclipboard(this);">
                    <span class="badge rounded-pill bg-secondary mb-1">Создано: {{$error->created_at}}</span>
                    <span class="badge rounded-pill bg-secondary mb-1">Обновлено: {{$error->updated_at}}</span>
                    <button type="button" class="btn-close float-end" aria-label="Close" value="{{$error->id}}" onclick="errordelete(this);"></button>
                    <div class="d-flex w-100 align-items-center justify-content-between">
                      <div class="col-10 mb-1 small">{{$error->error}}</div>
                      <span class="badge rounded-pill bg-secondary mb-1">x{{$error->count}}</span>
                    </div>
                  </a>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<script>
  function copytoclipboard(error)
  {
    event.preventDefault();
    navigator.clipboard.writeText(error.innerText);
  }
  function errordelete(error)
  {
    var formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', error.value);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '/server-management/error-delete');
    xhr.send(formData);
    xhr.onload = function() 
    {
      if (xhr.status != 200) { 
          console.log(`Ошибка ${xhr.status}: ${xhr.statusText}`);
      }
      else
      {
        location.reload();
      }
    };
  }
</script>
@endsection