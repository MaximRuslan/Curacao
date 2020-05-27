<?php

use Illuminate\Database\Seeder;

class UserStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\UserStatus::firstOrCreate([
            'title'    => 'Active',
            'title_nl' => 'Active',
            'title_es' => 'Active',
        ], [
            'role' => null
        ]);
        \App\Models\UserStatus::firstOrCreate([
            'title'    => 'Active-Gold',
            'title_nl' => 'Active-Gold',
            'title_es' => 'Active-Gold',
        ], [
            'role' => '1'
        ]);
        \App\Models\UserStatus::firstOrCreate([
            'title'    => 'Inactive-Deceased',
            'title_nl' => 'Inactive-Deceased',
            'title_es' => 'Inactive-Deceased',
        ], [
            'role' => '1'
        ]);
        \App\Models\UserStatus::firstOrCreate([
            'title'    => 'Inactive-Blacklisted',
            'title_nl' => 'Inactive-Blacklisted',
            'title_es' => 'Inactive-Blacklisted',
        ], [
            'role' => '1'
        ]);
        \App\Models\UserStatus::firstOrCreate([
            'title'    => 'Deactive',
            'title_nl' => 'Deactive',
            'title_es' => 'Deactive',
        ], [
            'role' => null
        ]);
    }
}
