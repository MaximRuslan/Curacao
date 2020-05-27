<?php

namespace App\ViewComposers;


use App\Library\Helper;
use App\Models\MerchantBranch;
use Illuminate\View\View;

class MerchantHeader
{
    public function compose(View $view)
    {
        $data = [];

        $merchant = Helper::authMerchantUser();

        $branches = MerchantBranch::pluckListing($merchant->id)->toArray();

        $branches = ['0' => __('keywords.all')] + $branches;

        $data['branches'] = $branches;

        $data['selected_branch'] = 0;

        if (session()->has('branch_id')) {
            $data['selected_branch'] = session('branch_id');
        }

        $data['merchant'] = $merchant;

        $view->with($data);
    }
}