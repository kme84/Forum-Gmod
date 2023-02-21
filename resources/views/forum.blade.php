@extends('layout')
@section('title')
    Форум
@endsection
@section('main_content')
<div class="container">
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
    
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Форум</li>
    </ol>
  </nav>
  @can('add', new App\Models\Chapter())
  <button class="mb-4 w-100 btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#AddChapterModal">Добавить раздел</button>
  @endcan
  @foreach ($chapters as $chapter)
  <div class="my-3 p-3 bg-body rounded shadow-sm">
    <h6 class="border-bottom pb-2 mb-0 d-flex justify-content-between">
      <span>{{$chapter->name}}</span>
      @if (Auth::user()->can('delete', $chapter) || Auth::user()->can('add', [new App\Models\Topic(), $chapter->id]))
      <div class="dropdown show">
        <a href="#" data-bs-toggle="dropdown" data-display="static">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                <circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle>
            </svg>
        </a>
        <div class="dropdown-menu">
            @can('add', [new App\Models\Topic(), $chapter->id])
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#EditChapterModal" onclick="editchapter.id.value={{$chapter->id}};editchapter.ord.value={{$chapter->ord}};editchapter.name.value='{{$chapter->name}}'">Изменить раздел</button>
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#AddTopicModal" onclick="addtopic.id.value={{$chapter->id}}">Добавить тему</button>
            @endcan
            @can('delete', $chapter)
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#RemoveChapterModal" onclick="removechapter.id.value={{$chapter->id}}">Удалить раздел</button>
            @endcan
        </div>
      </div>
      @endif
    </h6>
    @foreach ($topics as $topic)
    @if ($topic->chapter == $chapter->id)
    <div class="d-flex text-muted pt-3">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chat-right-fill me-2 flex-shrink-0" viewBox="0 0 16 16">
        <path d="M14 0a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12z"/>
      </svg>
      <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
        <div class="d-flex justify-content-between">
          <a class="text-decoration-none text-muted" href="forum/{{$topic->id}}"><strong class="text-gray-dark">{{$topic->name}}</strong></a>
          @can('delete', $topic)
          <div class="dropdown show">
            <a href="#" data-bs-toggle="dropdown" data-display="static">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>
                </svg>
            </a>
            <div class="dropdown-menu">
              <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#EditTopicModal" onclick="edittopic.id.value={{$topic->id}};edittopic.name.value='{{$topic->name}}';edittopic.ord.value={{$topic->ord}}">Изменить тему</button>
              <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#RemoveTopicModal" onclick="removetopic.id.value={{$topic->id}}">Удалить тему</button>
            </div>
          </div>
          @endcan
        </div>
        <span class="d-block">Количество постов</span>
      </div>
    </div>
    @endif
    @endforeach
    {{-- <small class="d-block text-end mt-3">
      <a href="#">All suggestions</a>
    </small> --}}
  </div> 
  @endforeach 
</div>

<x-modal modal-id='AddChapterModal' form-id='addchapter' action='forum/addchapter' method='post'>
    <x-slot:title>
        Добавление раздела
    </x-slot>
    <input type="text" name="name" id="name" placeholder="Название раздела" class="form-control">
    <x-slot:button class='btn-success'>
        Добавить
    </x-slot>
</x-modal>

<x-modal modal-id='EditChapterModal' form-id='editchapter' action='forum/editchapter' method='post'>
    <x-slot:title>
        Изменение раздела
    </x-slot>
    <input type="hidden" name="id" id="id">
    <input type="text" name="name" id="name" placeholder="Название раздела" class="form-control">
    <input type="number" name="ord" id="ord" placeholder="Порядок отображения" class="form-control">
    <x-slot:button class='btn-warning'>
        Изменить
    </x-slot>
</x-modal>

<x-modal modal-id='RemoveChapterModal' form-id='removechapter' action='forum/deletechapter' method='post'>
    <x-slot:title>
        Удаление раздела
    </x-slot>
    <input type="hidden" name="id" id="id">
    <p>Вы действительно хотите удалить раздел?</p>
    <x-slot:button class='btn-danger'>
        Удалить
    </x-slot>
</x-modal>

<x-modal modal-id='AddTopicModal' form-id='addtopic' action='forum/addtopic' method='post'>
    <x-slot:title>
        Добавление темы
    </x-slot>
    <input type="hidden" name="id" id="id">
    <input type="text" name="name" id="name" placeholder="Название темы" class="form-control">
    <x-slot:button class='btn-success'>
        Добавить
    </x-slot>
</x-modal>

<x-modal modal-id='EditTopicModal' form-id='edittopic' action='forum/edittopic' method='post'>
    <x-slot:title>
        Изменение темы
    </x-slot>
    <input type="hidden" name="id" id="id">
    <input type="text" name="name" id="name" placeholder="Название темы" class="form-control">
    <input type="number" name="ord" id="ord" placeholder="Порядок отображения" class="form-control">
    <x-slot:button class='btn-warning'>
        Изменить
    </x-slot>
</x-modal>

<x-modal modal-id='RemoveTopicModal' form-id='removetopic' action='forum/deletetopic' method='post'>
    <x-slot:title>
        Удаление темы
    </x-slot>
    <input type="hidden" name="id" id="id">
    <p>Вы действительно хотите удалить тему?</p>
    <x-slot:button class='btn-danger'>
        Удалить
    </x-slot>
</x-modal>
@endsection