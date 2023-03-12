<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // структура chapters.(*,create).(topics,view,edit,delete).(*,create).(posts,view,edit,delete).(*,create).(comments,view,edit,delete).(*,create).(view,edit,delete)
        $permissionForum = Permission::findOrCreate('forum.*'); // форум
        $permissionUsers = Permission::findOrCreate('users.*'); // список пользователей
        $permissionServersControl = Permission::findOrCreate('serverscontrol.*'); // управление серверами
        $permissionControlPanel = Permission::findOrCreate('controlpanel.*'); // панель управления

        //$role = Role::create(['name' => 'super-admin']);
        $role = Role::findOrCreate('super-admin');
        $role->syncPermissions([$permissionForum, $permissionUsers, $permissionServersControl, $permissionControlPanel]);

        $role = Role::findOrCreate('user');
        //$role->syncPermissions(['']);
    }
}
