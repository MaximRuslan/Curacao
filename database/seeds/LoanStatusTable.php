<?php

use Illuminate\Database\Seeder;

class LoanStatusTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('loan_status')->truncate();

        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Requested',
            'title_nl' => 'Requested',
            'title_es' => 'Requested',
        ], [
            'order' => '1'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'On Hold',
            'title_nl' => 'On Hold',
            'title_es' => 'On Hold',
        ], [
            'order' => '2'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Approved',
            'title_nl' => 'Approved',
            'title_es' => 'Approved',
        ], [
            'order' => '3'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Current',
            'title_nl' => 'Current',
            'title_es' => 'Current',
        ], [
            'order' => '4'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'In default',
            'title_nl' => 'In default',
            'title_es' => 'In default',
        ], [
            'order' => '5'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Debt collector',
            'title_nl' => 'Debt collector',
            'title_es' => 'Debt collector',
        ], [
            'order' => '6'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Paid in full - current',
            'title_nl' => 'Paid in full - current',
            'title_es' => 'Paid in full - current',
        ], [
            'order' => '7'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Paid in full - in default ',
            'title_nl' => 'Paid in full - in default ',
            'title_es' => 'Paid in full - in default ',
        ], [
            'order' => '8'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Paid in full - debt.coll',
            'title_nl' => 'Paid in full - debt.coll',
            'title_es' => 'Paid in full - debt.coll',
        ], [
            'order' => '9'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Write off',
            'title_nl' => 'Write off',
            'title_es' => 'Write off',
        ], [
            'order' => '10'
        ]);
        \App\Models\LoanStatus::firstOrCreate([
            'title'    => 'Declined',
            'title_nl' => 'Declined',
            'title_es' => 'Declined',
        ], [
            'order' => '11'
        ]);
    }
}
