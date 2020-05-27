<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(UserTable::class);
//        $this->call(RoleTable::class);
        $this->call(LoanStatusTable::class);
        $this->call(TransactionTypeTable::class);
        $this->call(UserStatus::class);
    }
}
