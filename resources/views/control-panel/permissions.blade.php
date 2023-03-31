@extends('control-panel.layout')
@section('title')
    Панель управления | Права
@endsection
@section('secondary_content')
<div class="col-md-9 col-lg-8 px-md-4" x-data='permissions'>
    <form action="/control-panel/permissions-role/edit" method="post">
        @csrf
        <div class="d-xl-flex mb-4">
            <div class="form-floating flex-fill me-2">
                <select class="form-select" name='permissionType' id="permissionType" aria-label="Select permission" x-model='permissionType_id' x-on:change="chapter_id='';post_id='';topic_id=''">
                    <option value=''>Не выбрано</option>
                    <template x-for="(permissionType, key) in permissionTypes" :key="key">
                        <option x-model='key' x-text='permissionType'></option>
                    </template>
                </select>
                <label for="floatingSelect">Выберите что ограничивать</label>
            </div>
            <div x-show="permissionType_id == 'forum'">
                <div class="d-xl-flex">
                    <div class="form-floating flex-fill me-2">
                        <select class="form-select" name='permissionChapter' id="permissionChapter" aria-label="Select chapter" x-model='chapter_id' x-on:change="post_id='';topic_id=''">
                            <option value=''>Не выбрано</option>
                            <template x-for="chapter in chapters" :key="chapter.id">
                                <option x-model='chapter.id' x-text='chapter.name'></option>
                            </template>
                        </select>
                        <label for="floatingSelect">Выберите раздел</label>
                    </div>
                    <div class="form-floating flex-fill me-2" x-show='chapter_id'>
                        <select class="form-select" name='permissionTopic' id="permissionTopic" aria-label="Select topic" x-model='topic_id'>
                            <option value=''>Не выбрано</option>
                            <template x-for="topic in filteredTopics" :key="topic.id">
                                <option x-model='topic.id' x-text='topic.name'></option>
                            </template>
                        </select>
                        <label for="floatingSelect">Выберите тему</label>
                    </div>
                    <div class="form-floating flex-fill" x-show='topic_id'>
                        <select class="form-select" name='permissionPost' id="permissionPost" aria-label="Select post" x-model='post_id'>
                            <option value=''>Не выбрано</option>
                            <template x-for="post in filteredPosts" :key="post.id">
                                <option x-model='post.id' x-text='post.name'></option>
                            </template>
                        </select>
                        <label for="floatingSelect">Выберите пост</label>
                    </div>
                </div>
            </div>
        </div>
        <div id='actionsRadio' x-show="role_id != '' && permissionType_id != ''">
            <template x-for="(action, key) in filteredActions" :key="key">
                <div class="d-flex">
                    <p class='me-auto' x-text='action.name'></p>
                    <div class="form-check me-3" x-data="{ id: $id('actionRadio' + key)}">
                        <input class="form-check-input" type="radio"  x-bind:name="key" x-bind:id="id" value="1" x-model="action.selected">
                        <label class="form-check-label" x-bind:for="id">
                            Да
                        </label>
                    </div>
                    <div class="form-check me-3" x-data="{ id: $id('actionRadio' + key)}">
                        <input class="form-check-input" type="radio"  x-bind:name="key" x-bind:id="id" value="0" x-model="action.selected">
                        <label class="form-check-label" x-bind:for="id">
                            Нет
                        </label>
                    </div>
                </div>
            </template>
        </div>
        <div class="d-flex justify-content-between">
            <div class="form-floating">
                <select class="form-select" name="role" id="role" aria-label="Select role" x-model='role_id'>
                    <option value=''>Не выбрано</option>
                    <template x-for="role in roles" :key="role.id">
                        <option x-model='role.id' x-text='role.name'></option>
                    </template>
                </select>
                <label for="floatingSelect">Выберите роль</label>
            </div>
            <button class="btn btn-primary" type="submit" x-bind:disabled="role_id == '' || permissionType_id == ''">Применить</button>
        </div>
    </form>
</div>
@push('scripts')
<script type="text/javascript">
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('permissions', () => ({
            permissionTypes:
            {
                forum: 'Форум',
                users: 'Пользователи',
                serverscontrol: 'Управление серверами',
                controlpanel: 'Панель управления'
            },
            permissionType_id: '',
            actions:
            {
                all: {name: 'Полные права', selected: '0'},
                view: {name: 'Просмотр', selected: '0'},
                create: {name: 'Создание', selected: '0'},
                edit: {name: 'Редактирование', selected: '0'},
                delete: {name: 'Удаление', selected: '0'},
            },
            actionsForPermissions:
            {
                forum: {all: true, view: true, create: true},
                chapters: {all: true, view: true, create: true, edit: true, delete: true},
                topics: {all: true, view: true, create: true, edit: true, delete: true},
                posts: {all: true, view: true, create: true, edit: true, delete: true},
                users: {all: true, view: true, edit: true},
                serverscontrol: {all: true, view: true, create: true, edit: true, delete: true},
                controlpanel: {all: true, view: true, create: true, edit: true, delete: true},
            },
            roles: {{ Js::from($roles) }},
            role_id: '',
            chapters: {{ Js::from($chapters) }},
            topics: {{ Js::from($topics) }},
            posts: {{ Js::from($posts) }},
            chapter_id: '',
            topic_id: '',
            post_id: '',

            get filteredActions()
            {
                if (this.role_id == '')
                {
                    return this.actions
                }
                const filteredActions = {}
                let checkPermission = this.permissionType_id
                let actionsForPermission = this.permissionType_id
                if (this.chapter_id != '')
                {
                    actionsForPermission = 'chapters'
                    checkPermission += '.'+this.chapter_id
                }
                if (this.topic_id != '')
                {
                    actionsForPermission = 'topics'
                    checkPermission += '.'+this.topic_id
                }
                if (this.post_id != '')
                {
                    actionsForPermission = 'posts'
                    checkPermission += '.'+this.post_id
                }
                for (const permission in this.actionsForPermissions[actionsForPermission])
                {
                    if (this.actions[permission])
                    {
                        this.actions[permission].selected = '0'
                        filteredActions[permission] = this.actions[permission]
                        for(const rolePermission of this.roles[this.role_id-1].permissions)
                        {
                            if (rolePermission.name.replace('*', 'all') == checkPermission+'.'+permission)
                            {
                                filteredActions[permission].selected = '1'
                            }
                        }
                    }

                }
                return filteredActions
            },

            get filteredTopics()
            {
                return this.topics.filter( i => i.chapter_id == this.chapter_id)
            },

            get filteredPosts()
            {
                return this.posts.filter( i => i.topic_id == this.topic_id)
            }
        }))
    });
</script>
@endpush
@endsection
