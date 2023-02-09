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
                <circle cx="12" cy="12" r="1"></circle>
                <circle cx="19" cy="12" r="1"></circle>
                <circle cx="5" cy="12" r="1"></circle>
            </svg>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            @can('add', [new App\Models\Topic(), $chapter->id])
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
            <div class="dropdown-menu dropdown-menu-right">
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
<!-- Modal AddChapter -->
<div class="modal fade" id="AddChapterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Добавление раздела</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form class="d-grid gap-3" id="addchapter" method="post" action="forum/addchapter" onsubmit="return this.addchapter.disabled=true;">
              @csrf
              <input type="text" name="name" id="name" placeholder="Название раздела" class="form-control">
          </form>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <input class="btn btn-success" name="addchapter" type="submit" form="addchapter" value="Добавить">
      </div>
      </div>
  </div>
</div>
<!-- Modal RemoveChapter -->
<div class="modal fade" id="RemoveChapterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Вы действительно хотите удалить раздел?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form class="d-grid gap-3" id="removechapter" method="post" action="forum/deletechapter" onsubmit="return this.removechapter.disabled=true;">
              @csrf
              <input type="hidden" name="id" id="id">
              <p>Вы действительно хотите удалить раздел?</p>
          </form>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <input class="btn btn-danger" name="removechapter" type="submit" form="removechapter" value="Удалить">
      </div>
      </div>
  </div>
</div>
<!-- Modal AddTopic -->
<div class="modal fade" id="AddTopicModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Добавление темы</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form class="d-grid gap-3" id="addtopic" method="post" action="forum/addtopic" onsubmit="return this.addtopic.disabled=true;">
              @csrf
              <input type="hidden" name="id" id="id">
              <input type="text" name="name" id="name" placeholder="Название темы" class="form-control">
          </form>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <input class="btn btn-success" name="addtopic" type="submit" form="addtopic" value="Добавить">
      </div>
      </div>
  </div>
</div>
<!-- Modal RemoveTopic -->
<div class="modal fade" id="RemoveTopicModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Вы действительно хотите удалить тему?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form class="d-grid gap-3" id="removetopic" method="post" action="forum/deletetopic" onsubmit="return this.removetopic.disabled=true;">
              @csrf
              <input type="hidden" name="id" id="id">
              <p>Вы действительно хотите удалить тему?</p>
          </form>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
          <input class="btn btn-danger" name="removetopic" type="submit" form="removetopic" value="Удалить">
      </div>
      </div>
  </div>
</div>
<script>

</script>
@endsection