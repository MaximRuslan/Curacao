<?php

return [
    [
        'name'  => 'keywords.dashboard',
        'route' => 'merchant.home.index',
        'icon'  => 'fa fa-desktop',
    ],
    [
        'name'  => 'keywords.payments',
        'route' => 'merchant.payments.index',
        'icon'  => 'fa fa-money',
    ],
    [
        'name'       => 'keywords.reconciliations',
        'route'      => 'merchant.reconciliations.index',
        'icon'       => 'fa fa-money',
        'permission' => 'reconciliation'
    ],
];