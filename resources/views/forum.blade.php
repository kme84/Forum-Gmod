@extends('layout')
@section('title')
    Форум
@endsection
@section('main_content')
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">Форум</li>
    </ol>
  </nav>
  @foreach ($chapters as $chapter)
    @can('forum.'.$chapter->id.'.view')
        <div class="my-3 p-3 bg-body rounded shadow">
            <h6 class="border-bottom pb-2 mb-0 d-flex justify-content-between">
                <span>{{$chapter->name}}</span>
            </h6>
            @foreach ($chapter->topics as $topic)
                @can('forum.'.$chapter->id.'.'.$topic->id.'.view')
                    <div class="d-flex text-muted pt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chat-right-fill me-2 flex-shrink-0" viewBox="0 0 16 16">
                            <path d="M14 0a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12z"/>
                        </svg>
                        <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                            <div class="d-flex justify-content-between">
                                <a class="text-decoration-none text-muted" href="forum/{{$topic->id}}"><strong class="text-gray-dark">{{$topic->name}}</strong></a>
                            </div>
                            <span class="d-block">Количество постов: {{$topic->size}}</span>
                        </div>
                    </div>
                @endcan
            @endforeach
            {{-- <small class="d-block text-end mt-3">
            <a href="#">All suggestions</a>
            </small> --}}
        </div>
    @endcan
  @endforeach
</div>
@endsection
