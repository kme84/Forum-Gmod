@extends('layout')
@section('title')
    Управление серверами
@endsection
@section('main_content')
<div class="container">
    <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link" href="/server-management/console/{{$server->id}}">Консоль</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/server-management/players/{{$server->id}}">Игроки</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/server-management/lua/{{$server->id}}">Lua</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/server-management/errors/{{$server->id}}">Ошибки</a>
        </li>
    </ul>
    <div class="list-group list-group-flush border-bottom scrollarea">
        <a href="#" class="list-group-item list-group-item-action py-3 lh-tight bg-primary text-white">
          <div class="d-flex w-100 align-items-center justify-content-between">
            <div class="col-10 mb-1 small">
                Koyomi Araragi|4|STEAM_0:0:27455336
    
                [igs-modification] addons/igs-modification/lua/igs/settings/config_sh.lua:106: attempt to index field 'S' (a nil value)
                  1. unknown - addons/igs-modification/lua/igs/settings/config_sh.lua:106
                   2. cl - [C]:-1
                    3. sh - autorun/l_ingameshop.lua:25
                     4. unknown - igs/launcher.lua:34
                      5. RunString - [C]:-1
                       6. sh - autorun/l_ingameshop.lua:41
                        7. unknown - autorun/l_ingameshop.lua:155
                         8. RunString - [C]:-1
                          9. cb - addons/igs-modification/lua/autorun/l_ingameshopmod.lua:48
                           10. onsuccess - addons/igs-modification/lua/autorun/l_ingameshopmod.lua:33
                            11. unknown - lua/includes/modules/http.lua:29</div>
            <small class="text-white">x10</small>
          </div>
        </a>
          <a href="#" class="list-group-item list-group-item-action py-3 lh-tight bg-warning">
            <div class="d-flex w-100 align-items-center justify-content-between">
              <div class="col-10 mb-1 small">
                  Koyomi Araragi|4|STEAM_0:0:27455336
      
                  [igs-modification] addons/igs-modification/lua/igs/settings/config_sh.lua:106: attempt to index field 'S' (a nil value)
                    1. unknown - addons/igs-modification/lua/igs/settings/config_sh.lua:106
                     2. cl - [C]:-1
                      3. sh - autorun/l_ingameshop.lua:25
                       4. unknown - igs/launcher.lua:34
                        5. RunString - [C]:-1
                         6. sh - autorun/l_ingameshop.lua:41
                          7. unknown - autorun/l_ingameshop.lua:155
                           8. RunString - [C]:-1
                            9. cb - addons/igs-modification/lua/autorun/l_ingameshopmod.lua:48
                             10. onsuccess - addons/igs-modification/lua/autorun/l_ingameshopmod.lua:33
                              11. unknown - lua/includes/modules/http.lua:29</div>
              <small>x14</small>
            </div>
          </a>
      </div>
</div>
@endsection