@extends('layout')
@section('title')
    Форум | Тема
@endsection
@section('main_content')
<div class="container">
    <button class="mb-4 w-100 btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#AddDiscussionModal">Добавить обсуждение</button>
    @can('delete', $topic)
    <button class="mb-4 w-100 btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#RemoveTopicModal">Удалить тему</button>
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
          <a href="#">Follow</a>
        </div>
        <span class="d-block">Количество сообщений</span>
      </div>
    </div>

    @endforeach
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
            <form class="d-grid gap-3" id="removetopic" method="post" action="deletetopic" onsubmit="return this.removetopic.disabled=true;">
                @csrf
                <input type="hidden" name="id" id="id" value="{{$topic->id}}">
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
<!-- Modal addpost -->
<div class="modal fade" id="AddDiscussionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-xl">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Добавление обсуждения</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <form class="d-grid gap-3" id="addpost" name="addpost" method="post" action="addpost" enctype="multipart/form-data" onsubmit="return this.addpost.disabled=true;">
            @csrf
            <input type="hidden" name="id" id="id" value="{{$topic->id}}">
            <input type="text" name="name" id="name" placeholder="Название обсуждения" class="form-control">
            <textarea name="editor" id="editor"></textarea>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
        <input class="btn btn-success" name="submit" type="submit" form="addpost" value="Добавить">
    </div>
    </div>
</div>
</div>
<script src="{{asset('ckeditor5-build-classic/ckeditor.js')}}"></script>
<script>
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

    ClassicEditor
        .create( document.querySelector( '#editor' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
        } )
        .catch( error => {
            console.log( error );
        } );
</script>
@endsection