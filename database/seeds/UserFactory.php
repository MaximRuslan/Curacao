<?php

use Illuminate\Database\Seeder;

class UserFactory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\User::class, 40)
            ->create()
            ->each(function ($u) {
                if ($u->role_id == '1') {
                    $u->assignRole(['admin']);
                } elseif ($u->role_id == '2') {
                    $u->assignRole(['manager']);
                } elseif ($u->role_id == '3') {
                    $u->assignRole(['client']);
                } elseif ($u->role_id == '4') {
                    $u->assignRole(['merchant']);
                }
                $u->userBanks()->save(factory(App\Models\UserBank::class)->make());
                $u->userBanks()->save(factory(App\Models\UserBank::class)->make());
                $u->userBanks()->save(factory(App\Models\UserBank::class)->make());
                $u->userBanks()->save(factory(App\Models\UserBank::class)->make());
                $u->userBanks()->save(factory(App\Models\UserBank::class)->make());
                $u->userWorks()->save(factory(App\Models\UserWork::class)->make());
                $u->userWorks()->save(factory(App\Models\UserWork::class)->make());
                $u->userWorks()->save(factory(App\Models\UserWork::class)->make());
                $u->userWorks()->save(factory(App\Models\UserWork::class)->make());
                $u->userWorks()->save(factory(App\Models\UserWork::class)->make());
                $u->userWorks()->save(factory(App\Models\UserWork::class)->make());
                $u->userReferences()->save(factory(App\Models\UserReference::class)->make());
                $u->userReferences()->save(factory(App\Models\UserReference::class)->make());
                $u->userReferences()->save(factory(App\Models\UserReference::class)->make());
                $u->userReferences()->save(factory(App\Models\UserReference::class)->make());
                $u->userReferences()->save(factory(App\Models\UserReference::class)->make());
                $u->userReferences()->save(factory(App\Models\UserReference::class)->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'telephone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'telephone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'telephone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'telephone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'telephone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'telephone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'cellphone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'cellphone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'cellphone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'cellphone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'cellphone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'cellphone')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'emails')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'emails')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'emails')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'emails')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'emails')->make());
                $u->userInfos()->save(factory(App\Models\UserInfo::class, 'emails')->make());
            });
    }
}
