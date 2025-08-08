<?php

namespace App\Listeners;

use App\Events\AssignRole;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AssignRole $event): void
    {
        $user = $event->user;
        $role = $event->roleName;

        $user->syncRoles($role);
    }
}
