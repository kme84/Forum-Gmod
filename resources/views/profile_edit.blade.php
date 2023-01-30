@extends('layout')
@section('title')
    Редактирование профиля
@endsection
@section('main_content')
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />

<div class="container p-0">
    <h1 class="h3 mb-3">Настройки</h1>
    <div class="row">
        <div class="col-md-5 col-xl-4">

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Настройки профиля</h5>
                </div>

                <div class="list-group list-group-flush" role="tablist">
                    <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#account" role="tab">
                      Учетная запись
                    </a>
                    <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#password" role="tab">
                      Пароль
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-7 col-xl-8">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="tab-content">
                <div class="tab-pane fade show active" id="account" role="tabpanel">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Публичная информация</h5>
                        </div>
                        <div class="card-body">
                            <form action="/profile/edit/public" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="inputUsername">Псевдоним</label>
                                            <input type="text" class="form-control" id="inputUsername" name="inputUsername" placeholder="Псевдоним" value="{{ $user->name }}">
                                        </div>
                                        <div class="form-group mt-2">
                                            <label for="inputUsername">О себе</label>
                                            <textarea rows="2" class="form-control" id="inputBio" name="inputBio" placeholder="Расскажите что-нибудь о себе">{{ $user->bio }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <img name="avatarImage" id="avatarImage" src={{$user->avatar ? asset('/storage/'.$user->avatar) : asset('/storage/static/noavatar.png')}} class="rounded-circle img-responsive mt-2 object-fit-cover" width="128" height="128">
                                            <div class="mt-2">
                                                <span onclick="uploadAvatar.click()" class="btn btn-primary"><i class="fa fa-upload"></i></span>
                                                <input type="file" name="uploadAvatar" id="uploadAvatar" hidden>
                                            </div>
                                            <small>For best results, use an image at least 128px by 128px in .jpg format</small>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="id" id="id" value="{{$user->id}}">
                                <button type="submit" class="btn btn-primary mt-2">Сохранить</button>
                            </form>

                        </div>
                    </div>

                    <div class="card mt-5">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Личная информация</h5>
                        </div>
                        <div class="card-body">
                            <form action="/profile/edit/private" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputFirstName">Имя</label>
                                        <input type="text" class="form-control" id="inputFirstName" name="inputFirstName" placeholder="First name" value="{{ $user->firstname }}">
                                    </div>
                                    <div class="form-group col-md-6 mt-2">
                                        <label for="inputLastName">Фамилия</label>
                                        <input type="text" class="form-control" id="inputLastName" name="inputLastName" placeholder="Last name" value="{{ $user->lastname }}">
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="inputEmail4">Email</label>
                                    <input type="email" class="form-control" id="inputEmail4" placeholder="Email" value="{{ $user->email }}" disabled>
                                </div>
                                <div class="form-row mt-2">
                                    <div class="form-group col-md-4">
                                        <label for="inputState">Пол</label>
                                        <select id="inputState" name="inputState" class="form-control">
                                            <option {{!$user->sex ? "selected" : null}}>Выберите...</option>
                                            <option value='M' {{$user->sex == 'M' ? "selected" : null}}>Мужской</option>
                                            <option value='W' {{$user->sex == 'W' ? "selected" : null}}>Женский</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" name="id" id="id" value="{{$user->id}}">
                                <button type="submit" class="btn btn-primary mt-2">Сохранить</button>
                            </form>

                        </div>
                    </div>

                </div>
                <div class="tab-pane fade" id="password" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Пароль</h5>

                            <form action="/profile/edit/password" method="POST">
                                @csrf
                                @if($user->id === Auth::id())
                                <div class="form-group">
                                    <label for="inputPasswordCurrent">Текущий пароль</label>
                                    <input type="password" class="form-control" id="inputPasswordCurrent" name="inputPasswordCurrent">
                                    @if (Route::has('password.request'))
                                        <small><a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a></small>
                                    @endif
                                </div>
                                @endif
                                <div class="form-group mt-2">
                                    <label for="inputPasswordNew">Новый пароль</label>
                                    <input type="password" class="form-control" id="inputPasswordNew" name="inputPasswordNew">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="inputPasswordNew2">Подтвердите пароль</label>
                                    <input type="password" class="form-control" id="inputPasswordNew2" name="inputPasswordNew2">
                                </div>
                                <input type="hidden" name="id" id="id" value="{{$user->id}}">
                                <button type="submit" class="btn btn-primary mt-2">Сохранить</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    uploadAvatar.addEventListener('change', (e) => {
        avatarImage.src = (window.URL ? URL : webkitURL).createObjectURL(uploadAvatar.files[0]);
    });
</script>
@endsection