<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RoleTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \App\Models\Role::create([
            'name' => 'admin'
        ]);
        \App\Models\Role::create([
            'name' => 'manager'
        ]);
        \App\Models\Role::create([
            'name' => 'client'
        ]);
        $admin = \App\Models\User::find(1);
        $admin->assignRole('admin');

        $manager = \App\Models\User::find(2);
        $manager->assignRole('manager');

        $client = \App\Models\User::find(3);
        $client->assignRole('client');
    }
}
