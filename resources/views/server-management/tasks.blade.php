@extends('server-management.layout')
@section('title')
    Управление серверами | Задачи
@endsection
@section('secondary_content')
    <div class="row">
      <div class="col-md-5 col-xl-4">
          <div class="card">
              <div class="card-header">
                  <h5 class="card-title mb-0">Статус задачи</h5>
              </div>
              <div class="list-group list-group-flush" role="tablist">
                  <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#server" role="tab">
                    В процессе
                  </a>
                  <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#client" role="tab">
                    Выполнено
                  </a>
              </div>
          </div>
      </div>
      <div class="col-md-7 col-xl-8">
        <button class="mb-4 w-100 btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#AddTaskModal">Добавить задачу</button>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="server" role="tabpanel">
            <div id="taskListActive" class="list-group list-group-flush border-bottom scrollarea">
              @foreach ($tasks as $task)
                @if($task->status == 1)
                <div id="task-{{$task->id}}" class="card text-white bg-secondary mb-3" style="max-width: 100rem;">
                  <div class="card-header">
                      <span class="badge rounded-pill bg-dark mb-1">Автор: {{$users[$task->author-1]->name}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">Создано: {{$task->created_at}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">Обновлено: {{$task->updated_at}}</span>
                      <span @class([
                        'badge',
                        'rounded-pill',
                        'bg-success' => $task->priority == 1,
                        'bg-warning' => $task->priority == 2,
                        'bg-danger' => $task->priority == 3,
                      ])>{{$priorities[$task->priority]}}</span>
                      <button type="button" class="btn-close float-end" aria-label="Close" value="{{$task->id}}" onclick="taskdelete(this);"></button>
                  </div>
                  <div class="card-body">
                    <h5 class="card-title">{{$task->title}}</h5>
                    <p class="card-text">{{$task->description}}</p>
                  </div>
                  <div class="card-footer bg-transparent" style="border-color: rgba(0, 0, 0, 0.125);">
                    <button type="button" class="btn btn-success float-end" value="{{$task->id}}" onclick="taskchangestate(this);">Подтвердить</button>
                  </div>
                </div>
                @endif
              @endforeach
            </div>
          </div>
          <div class="tab-pane fade" id="client" role="tabpanel">
            <div id="taskListComplete" class="list-group list-group-flush border-bottom scrollarea">
              @foreach ($tasks as $task)
                @if($task->status == 2)
                <div id="task-{{$task->id}}" class="card text-white bg-secondary mb-3" style="max-width: 100rem;">
                  <div class="card-header">
                      <span class="badge rounded-pill bg-dark mb-1">Автор: {{$users[$task->author-1]->name}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">Создано: {{$task->created_at}}</span>
                      <span class="badge rounded-pill bg-dark mb-1">Обновлено: {{$task->updated_at}}</span>
                      <span @class(['badge', 'rounded-pill', 'bg-success' => $task->priority == 1, 'bg-warning' => $task->priority == 2, 'bg-danger' => $task->priority == 3])>{{$priorities[$task->priority]}}</span>
                      <button type="button" class="btn-close float-end" aria-label="Close" value="{{$task->id}}" onclick="taskdelete(this);"></button>
                  </div>
                  <div class="card-body">
                    <h5 class="card-title">{{$task->title}}</h5>
                    <p class="card-text">{{$task->description}}</p>
                  </div>
                  <div class="card-footer bg-transparent" style="border-color: rgba(0, 0, 0, 0.125);">
                    <button type="button" class="btn btn-warning float-end" value="{{$task->id}}" onclick="taskchangestate(this);">Возобновить</button>
                  </div>
                </div>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <x-modal modal-id='AddTaskModal' form-id='addtask' action='/server-management/task-add' method='post' enctype="multipart/form-data">
    <x-slot:title>
        Добавление задачи
    </x-slot>
    <input type="hidden" name="server" id="server" value="{{$server->id}}">
    <input type="text" name="title" id="title" placeholder="Наименование" class="form-control">
    <input type="text" name="description" id="description" placeholder="Описание" class="form-control">
    <select class="form-select" id="priority" name="priority" aria-label="Default select example">
        <option selected>Приоритет</option>
        <option value="1">Низкий</option>
        <option value="2">Средний</option>
        <option value="3">Высокий</option>
    </select>
    <x-slot:button class='btn-success'>
        Добавить
    </x-slot>
  </x-modal>
  
@push('scripts')
<script type="text/javascript">
  function taskdelete(task)
  {
    var formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', task.value);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '/server-management/task-delete');
    xhr.send(formData);
    xhr.onload = function() 
    {
      if (xhr.status != 200) { 
          console.log(`Ошибка ${xhr.status}: ${xhr.statusText}`);
      }
      else
      {
        var task_el = document.querySelector('#task-'+task.value);
        task_el.remove();
      }
    }
  }
  let waitanswer = false;
  function taskchangestate(task)
  {
    if (waitanswer){return}
    waitanswer = true;
    var formData = new FormData();
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('id', task.value);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '/server-management/task-change');
    xhr.send(formData);
    xhr.onload = function() 
    {
      waitanswer = false;
      if (xhr.status != 200) { 
          console.log(`Ошибка ${xhr.status}: ${xhr.statusText}`);
      }
      else
      {
        var task_el = document.querySelector('#task-'+task.value);
        if (task_el.parentElement.id == 'taskListActive')
        {
          let complete = document.querySelector('#taskListComplete');
          complete.append(task_el);
          task.innerText = 'Возобновить';
          task.classList.remove('btn-success');
          task.classList.add('btn-warning');
        }
        else
        {
          let active = document.querySelector('#taskListActive');
          active.append(task_el);
          task.innerText = 'Подтвердить';
          task.classList.remove('btn-warning');
          task.classList.add('btn-success');
        }
      }
    };
  }
</script>
@endpush
@endsection