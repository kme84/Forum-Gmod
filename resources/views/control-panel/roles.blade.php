@extends('control-panel.layout')
@section('title')
    Панель управления | Права
@endsection
@section('secondary_content')
<div class="col-md-9 col-lg-8 px-md-4" x-data='roles'>
    <form action="/control-panel/roles-user/edit" method="post">
        @csrf
        <div class="d-xl-flex mb-4">
            <div class="form-floating flex-fill me-2">
                <select class="form-select" name='user' id="user" aria-label="Select permission">
                    <option value=''>Не выбрано</option>
                    <template x-for="(user, key) in users" :key="key">
                        <option x-model='key' x-text='user.name'></option>
                    </template>
                </select>
                <label for="floatingSelect">Кому выдать роли</label>
            </div>
        </div>
        <div id='rolesRadio'>
            <template x-for="(role, key) in roles" :key="key">
                <div class="d-flex">
                    <p class='me-auto' x-text='role.name'></p>
                    <div class="form-check me-3" x-data="{ id: $id('actionRadio' + key)}">
                        <input class="form-check-input" type="radio"  x-bind:name="'role['+role.name+']'" x-bind:id="id" value="1" x-model="role.selected">
                        <label class="form-check-label" x-bind:for="id">
                            Да
                        </label>
                    </div>
                    <div class="form-check me-3" x-data="{ id: $id('actionRadio' + key)}">
                        <input class="form-check-input" type="radio"  x-bind:name="'role['+role.name+']'" x-bind:id="id" value="0" x-model="role.selected">
                        <label class="form-check-label" x-bind:for="id">
                            Нет
                        </label>
                    </div>
                </div>
            </template>
        </div>
        <div class="d-flex">
            <button class="btn btn-primary" type="submit">Применить</button>
        </div>
    </form>
</div>
@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('roles', () => ({
            roles: {{ Js::from($roles) }},
            users: {{ Js::from($users) }},

        }))
    });
</script>
@endpush
@endsection
