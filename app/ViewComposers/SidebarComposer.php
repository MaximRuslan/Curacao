<?php

namespace app\ViewComposers;

use App\Models\LoanStatus;
use Illuminate\View\View;

class SidebarComposer
{

    public function compose(View $view)
    {
        $data = [];

        $data['loan_statuses'] = LoanStatus::select('*')->orderBy('order', 'asc');
        $data['loan_statuses'] = $data['loan_statuses']->get();

        $view->with($data);
    }
}
