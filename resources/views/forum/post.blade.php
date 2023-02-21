@extends('layout')
@section('title')
    Форум | {{$post->title}}
@endsection
@section('main_content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/forum">Форум</a></li>
            <li class="breadcrumb-item"><a href="/forum/{{$topic->id}}">{{$topic->name}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{$post->title}}</li>
        </ol>
    </nav>

    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex w-100 align-items-center">
                    <img src={{$author->avatar ? asset('/storage/'.$author->avatar) : asset('/storage/static/noavatar.png')}} class="d-block ui-w-40 rounded-circle bg-light border border-secondary" width="96" height="96">
                    <div class="ms-3">
                        <a class="text-decoration-none" href="javascript:void(0)" data-abc="true">{{$author->name}}</a>
                    </div>
                    {{-- <div class="text-muted small me-3 position-absolute end-0">
                        <div>Member since <strong>01/1/2019</strong></div>
                        <div><strong>134</strong> posts</div>
                    </div> --}}
                </div>
            </div>
            <div class="card-body ck-content" id='content-' name='content-' author="{{$author->name}}">
                {!!$post->content!!}
            </div>
            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center">
                <div class="text-muted small">📅 {{$post->created_at}}</div>
                <button type="button" class="btn btn-primary" onclick="comment(this.value);" value="">Ответить</button>
            </div>
        </div>

        @foreach ($comments as $key => $comment)
            <div class="card d-flex flex-row mb-4">
                <div class="card-header d-flex flex-column align-items-center justify-content-center w-25">
                    <img src={{$users[$key]->avatar ? asset('/storage/'.$users[$key]->avatar) : asset('/storage/static/noavatar.png')}} class="d-block ui-w-40 rounded-circle bg-light border border-secondary" width="96" height="96">
                    <a class="text-decoration-none" href="javascript:void(0)" data-abc="true">{{$users[$key]->name}}</a>
                </div>
                <div class="card-body d-flex flex-column justify-content-between w-75">
                    <div id='content-{{$key}}' name='content-{{$key}}' author="{{$users[$key]->name}}" class="ck-content">
                        {!!$comment->content!!}
                    </div>
                    <hr>
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="text-muted small me-auto">📅 {{$comment->created_at}}</div>
                        @can('delete', $comment)
                        <form action='deletecomment' method='POST' name='deletecomment' id='deletecomment' enctype="multipart/form-data" onsubmit="return this.deletecomment.disabled=true;">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{$comment->id}}">
                            <input class="btn btn-danger ms-2" name="submit" type="submit" value="Удалить">
                        </form>
                        @endcan
                        <button class="btn btn-primary ms-2" name="reply" id="reply" type="button" onclick="comment(this.value);" value="{{$key}}">Ответить</button>
                    </div>
                </div>
            </div>
        @endforeach


        <div class="card d-flex flex-row mb-4">
            <div class="card-header d-flex flex-column align-items-center justify-content-center w-25">
                <img src={{Auth::user()->avatar ? asset('/storage/'.Auth::user()->avatar) : asset('/storage/static/noavatar.png')}} class="d-block ui-w-40 rounded-circle bg-light border border-secondary" width="96" height="96">
                <a class="text-decoration-none" href="javascript:void(0)" data-abc="true">{{Auth::user()->name}}</a>
            </div>
            <div class="card-body w-75">
                <form action='addcomment' method='POST' name='addcomment' id='addcomment' enctype="multipart/form-data" onsubmit="return this.addcomment.disabled=true;">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{$id}}">
                    <textarea name="editor" id="editor"></textarea>
                    <div class="d-flex flex-row-reverse"><input class="btn btn-primary mt-2" name="submit" type="submit" value="Отправить"></div>

                </form>
            </div>
        </div>

    </div>
</div>
@push('scripts')
<script src="{{asset('ckeditor5-build-classic/ckeditor.js')}}"></script>
<script type="text/javascript">
    var myeditor;
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
        .then( editor => {
            myeditor = editor;
        } )
        .catch( error => {
            console.log( error );
        } );
    });
    function comment(id)
    {
        var elem = document.querySelector( '#content-' + id );
        const viewFragment = myeditor.data.processor.toView( "<blockquote>" + elem.attributes.author.value + ":\n" + elem.innerHTML + "</blockquote>");
        const modelFragment = myeditor.data.toModel( viewFragment );
        myeditor.model.insertContent( modelFragment );

        document.addcomment.scrollIntoView();
    }
</script>
@endpush
@endsection
