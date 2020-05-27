<?php

namespace app\ViewComposers;

use App\Models\Country;
use Illuminate\View\View;

class HeaderComposer
{

    public function compose(View $view)
    {
        $data = [];
        $data['country'] = Country::pluck('name', 'id')->toArray();
        $data['selected_country'] = session()->has('country') ? session()->get('country') : '0';
        $view->with($data);
    }
}
