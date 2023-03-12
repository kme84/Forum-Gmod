<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GiveRoleNewUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $role = Role::findOrCreate('user');
        $event->user->assignRole($role);
        $premission = Permission::findOrCreate('users.'.$event->user->id.'.*');
        $event->user->givePermissionTo($premission);
        $premission = Permission::findOrCreate('users.'.$event->user->id.'.view');
        $role->givePermissionTo($premission);
    }
}
