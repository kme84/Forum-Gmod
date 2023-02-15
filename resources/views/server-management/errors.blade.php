@extends('server-management.layout')
@section('title')
    Управление серверами | Ошибки
@endsection
@section('secondary_content')
    <div class="row">
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
                <div id='error-{{$error->id}}' class="card text-white bg-primary mb-3" style="max-width: 100rem;">
                  <div class="card-header">
                      <span class="badge rounded-pill bg-dark mb-1">Создано: {{$error->created_at}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">Обновлено: {{$error->updated_at}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">x{{$error->count}}</span>
                      <div class="dropdown show float-end">
                        <a href="#" class='link-dark' data-bs-toggle="dropdown" data-display="static">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                <circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle>
                            </svg>
                        </a>
                        <div id='dropdownmenu-{{$error->id}}' class="dropdown-menu">
                          <button class="dropdown-item" onclick="createtask({{$error->id}}, {{$server->id}}, 'server');">Создать задачу</button>
                          <button class="dropdown-item" onclick="copytoclipboard({{$error->id}});">Копировать</button>
                          <button class="dropdown-item" onclick="errordelete({{$error->id}});">Удалить</button>
                        </div>
                      </div>
                  </div>
                  <div class="card-body">
                    <p class="card-text">{{$error->error}}</p>
                  </div>
                </div>
                @endif
              @endforeach
            </div>
          </div>
          <div class="tab-pane fade" id="client" role="tabpanel">
            <div class="list-group list-group-flush border-bottom scrollarea">
              @foreach ($errors as $error)
                @if ($error->realm == 'client')
                <div id='error-{{$error->id}}' class="card text-white bg-warning mb-3" style="max-width: 100rem;">
                  <div class="card-header">
                      <span class="badge rounded-pill bg-dark mb-1">Создано: {{$error->created_at}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">Обновлено: {{$error->updated_at}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">x{{$error->count}}</span>
                      <div class="dropdown show float-end">
                        <a href="#" class='link-dark' data-bs-toggle="dropdown" data-display="static">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                                <circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle>
                            </svg>
                        </a>
                        <div id='dropdownmenu-{{$error->id}}' class="dropdown-menu">
                          <button class="dropdown-item" onclick="createtask({{$error->id}}, {{$server->id}}, 'client');">Создать задачу</button>
                          <button class="dropdown-item" onclick="copytoclipboard({{$error->id}});">Копировать</button>
                          <button class="dropdown-item" onclick="errordelete({{$error->id}});">Удалить</button>
                        </div>
                      </div>
                  </div>
                  <div class="card-body">
                    <p class="card-text">{{$error->error}}</p>
                  </div>
                </div>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
@push('scripts')
<script>
  function createtask(error, server, realm)
  {
    var error_el = document.querySelector('#error-'+error);
    var dropdown_el = document.querySelector('#dropdownmenu-'+error);
    var content = dropdown_el.innerHTML;
    dropdown_el.innerHTML = '';

    var formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('server', server);
    formData.append('title', realm);
    formData.append('description', error_el.innerText);
    formData.append('priority', 2);

    dropdown_el.innerHTML = content;

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '/server-management/task-add');
    xhr.send(formData);
    xhr.onload = function() 
    {
      if (xhr.status != 200) { 
          console.log(`Ошибка ${xhr.status}: ${xhr.statusText}`);
      }
      else
      {
        errordelete(error);
      }
    }
  }
  function copytoclipboard(error)
  {
    var error_el = document.querySelector('#error-'+error);
    var dropdown_el = document.querySelector('#dropdownmenu-'+error);
    var content = dropdown_el.innerHTML;
    dropdown_el.innerHTML = '';
    navigator.clipboard.writeText(error_el.innerText);
    dropdown_el.innerHTML = content;
  }
  function errordelete(error)
  {
    var formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', error);

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
        document.querySelector('#error-'+error).remove();
      }
    };
  }
</script>
@endpush
@endsection