@extends('layout')
@section('title')
    Форум | {{$topic->name}}
@endsection
@section('main_content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/forum">Форум</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$topic->name}}</li>
        </ol>
    </nav>
    @can('add', [new App\Models\Post(), $topic->id])
    <button class="mb-4 w-100 btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#AddPostModal">Добавить обсуждение</button>
    @endcan
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @foreach ($posts as $post)
    <div class="d-flex text-muted pt-3">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chat-right-fill me-2 flex-shrink-0" viewBox="0 0 16 16">
        <path d="M14 0a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12z"/>
      </svg>
      <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
        <div class="d-flex justify-content-between">
          <a class="text-decoration-none text-muted" href="post/{{$post->id}}"><strong class="text-gray-dark">{{$post->title}}</strong></a>
          @can('delete', $post)
          <div class="dropdown show">
            <a href="#" data-bs-toggle="dropdown" data-display="static">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle">
                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>
                </svg>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#EditPostModal" onclick="editpost.id.value={{$post->id}};editpost.name.value='{{$post->title}}';editpost.ord.value={{$post->ord}}">Изменить пост</button>
              <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#RemovePostModal" onclick="removepost.id.value={{$post->id}}">Удалить пост</button>
            </div>
          </div>
          @endcan
          {{-- <a href="#">Follow</a> --}}
        </div>
        <span class="d-block">Количество сообщений</span>
      </div>
    </div>

    @endforeach
</div>
<x-modal modal-id='AddPostModal' form-id='addpost' action='addpost' method='post' enctype="multipart/form-data">
    <x-slot:title class='modal-lg'>
        Добавление обсуждения
    </x-slot>
    <input type="hidden" name="id" id="id" value="{{$topic->id}}">
    <input type="text" name="name" id="name" placeholder="Название обсуждения" class="form-control">
    <textarea name="editor" id="editor"></textarea>
    <x-slot:button class='btn-success'>
        Добавить
    </x-slot>
</x-modal>

<x-modal modal-id='EditPostModal' form-id='editpost' action='editpost' method='post'>
    <x-slot:title>
        Изменение обсуждения
    </x-slot>
    <input type="hidden" name="id" id="id">
    <input type="text" name="name" id="name" placeholder="Название обсуждения" class="form-control">
    <input type="number" name="ord" id="ord" placeholder="Порядок отображения" class="form-control">
    <x-slot:button class='btn-success'>
        Изменить
    </x-slot>
</x-modal>

<x-modal modal-id='RemovePostModal' form-id='removepost' action='deletepost' method='post'>
    <x-slot:title>
        Удаление обсуждения
    </x-slot>
    <input type="hidden" name="id" id="id">
    <p>Вы действительно хотите удалить тему?</p>
    <x-slot:button class='btn-success'>
        Удалить
    </x-slot>
</x-modal>

@push('scripts')
<script src="{{asset('ckeditor5-build-classic/ckeditor.js')}}"></script>
<script type="text/javascript">
    class MyUploadAdapter {
        constructor( loader ) {
            this.loader = loader;
        }
        upload() {         
            return this.loader.file
                .then( file => new Promise( ( resolve, reject ) => {
                    this._initRequest();
                    this._initListeners( resolve, reject, file );
                    this._sendRequest( file );
                } ) );
        }
        abort() {
            if ( this.xhr ) {
                this.xhr.abort();
            }
        }
        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();

            xhr.open( 'POST', '{{route('upload', ['_token' => csrf_token() ])}}', true );
            xhr.responseType = 'json';
        }

        _initListeners( resolve, reject, file ) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload file: ${ file.name }.`;

            xhr.addEventListener( 'error', () => reject( genericErrorText ) );
            xhr.addEventListener( 'abort', () => reject() );
            xhr.addEventListener( 'load', () => {
                const response = xhr.response;

                if ( !response || response.error ) {
                    return reject( response && response.error ? response.error.message : genericErrorText );
                }

                resolve( {
                    default: response.url
                } );
            } );

            if ( xhr.upload ) {
                xhr.upload.addEventListener( 'progress', evt => {
                    if ( evt.lengthComputable ) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                } );
            }
        }

        _sendRequest( file ) {
            const data = new FormData();
            data.append( 'upload', file );

            this.xhr.send( data );
        }
    }

    function MyCustomUploadAdapterPlugin( editor ) {
        editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
            return new MyUploadAdapter( loader );
        };
    }
    document.addEventListener('DOMContentLoaded', () =>
    {
    ClassicEditor
        .create( document.querySelector( '#editor' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
        } )
        .catch( error => {
            console.log( error );
        } );
    });
</script>
@endpush
@endsection