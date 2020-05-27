<?php

use Illuminate\Database\Seeder;

class TransactionTypeTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('transaction_types')->truncate();

        \App\Models\TransactionType::firstOrCreate([
            'title'    => 'Payments',
            'title_nl' => 'Payments',
            'title_es' => 'Payments',
        ]);
        \App\Models\TransactionType::firstOrCreate([
            'title'    => 'Write offs',
            'title_nl' => 'Write offs',
            'title_es' => 'Write offs',
        ]);
        \App\Models\TransactionType::firstOrCreate([
            'title'    => 'Correction',
            'title_nl' => 'Correction',
            'title_es' => 'Correction',
        ]);
        \App\Models\TransactionType::firstOrCreate([
            'title'    => 'Credit',
            'title_nl' => 'Credit',
            'title_es' => 'Credit',
        ]);
    }
}
