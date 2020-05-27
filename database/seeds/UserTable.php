<?php

use Illuminate\Database\Seeder;

class UserTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'firstname'  => 'super',
            'lastname'   => 'admin',
            'email'      => 'admin@gmail.com',
            'role_id'    => '1',
            'password'   => bcrypt('123456'),
            'department' => '1',
            'territory'  => '1',
            'status'     => '1',
        ]);
        \App\Models\User::create([
            'firstname'  => 'test',
            'lastname'   => 'manager',
            'email'      => 'manager@gmail.com',
            'role_id'    => '2',
            'password'   => bcrypt('123456'),
            'department' => '1',
            'territory'  => '1',
            'status'     => '1',
        ]);
        \App\Models\User::create([
            'firstname'  => 'test',
            'lastname'   => 'client',
            'email'      => 'client@gmail.com',
            'role_id'    => '3',
            'password'   => bcrypt('123456'),
            'department' => '1',
            'territory'  => '1',
            'status'     => '1',
        ]);
    }
}
