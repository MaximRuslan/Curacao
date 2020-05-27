<?php

namespace app\ViewComposers;

use App\Models\Country;
use Illuminate\View\View;

class ClientHeaderComposer
{

    public function compose(View $view)
    {
        $data = [];
        if (auth()->check()) {
            $data['country'] = Country::find(auth()->user()->country);
        }
        $view->with($data);
    }
}
