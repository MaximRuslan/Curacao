<?php

return [
    [
        'name'  => 'Dashboard',
        'route' => 'admin1.home',
        'icon'  => 'fa fa-desktop',
        'roles' => 'super admin|admin|processor|auditor|debt collector|loan approval|credit and processing',
    ],
    [
        'name'   => 'Users',
        'icon'   => 'fa fa-user',
        'roles'  => 'super admin|admin|processor|auditor|debt collector|loan approval|credit and processing',
        'values' => [
            [
                'name'  => 'Cockpit',
                'route' => 'admin1.cockpit.index',
                'roles' => 'super admin|admin',
            ],
            [
                'name'  => 'Wallet',
                'route' => 'admin1.wallet.index',
                'roles' => 'super admin|admin',
            ],
            [
                'name'  => 'Manage',
                'route' => 'admin1.users.index',
                'roles' => 'super admin|admin|processor|auditor|debt collector|loan approval|credit and processing',
            ],
            [
                'name'  => 'WEB Registrations',
                'route' => 'admin1.web-registrations.index',
                'roles' => 'super admin|admin|processor|auditor|debt collector|loan approval|credit and processing',
            ],
        ],
    ],
    [
        'name'   => 'Bundles',
        'icon'   => 'fa fa-vcard',
        'roles'  => 'super admin|admin|processor|debt collector|loan approval|credit and processing',
        'values' => [
            [
                'name'  => 'All',
                'route' => 'admin1.loans.index',
                'roles' => 'super admin|admin',
            ],
            [
                'name'         => 'Requested',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=1',
                'roles'        => 'super admin|admin|loan approval',
            ],
            [
                'name'         => 'Pre Approved',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=12',
                'roles'        => 'super admin|admin|loan approval',
            ],
            [
                'name'         => 'On Hold',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=2',
                'roles'        => 'super admin|admin|loan approval',
            ],
            [
                'name'         => 'Approved',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=3',
                'roles'        => 'super admin|admin|loan approval|credit and processing',
            ],
            [
                'name'  => 'To Assign',
                'route' => 'admin1.loans.assign',
                'roles' => 'super admin|admin',
            ],
            [
                'name'         => 'Current',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=4',
                'roles'        => 'super admin|admin|processor|loan approval|credit and processing',
            ],
            [
                'name'         => 'In default',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=5',
                'roles'        => 'super admin|admin|processor',
            ],
            [
                'name'         => 'Debt collector',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=6',
                'roles'        => 'super admin|admin|processor|debt collector',
            ],
            [
                'name'         => 'Paid in full - current',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=7',
                'roles'        => 'super admin|admin|processor',
            ],
            [
                'name'         => 'Paid in full - in default ',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=8',
                'roles'        => 'super admin|admin|processor|debt collector',
            ],
            [
                'name'         => 'Paid in full - debt.coll',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=9',
                'roles'        => 'super admin|admin|processor|debt collector',
            ],
            [
                'name'         => 'Write off',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=10',
                'roles'        => 'super admin|admin|processor',
            ],
            [
                'name'         => 'Declined',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=11',
                'roles'        => 'super admin|admin|processor|loan approval|credit and processing',
            ],
            [
                'name'         => 'Deleted',
                'route'        => 'admin1.loans.index',
                'query_string' => 'status=deleted',
                'roles'        => 'super admin|admin',
            ],
        ],
    ],
    [
        'name'  => 'My Clients',
        'route' => 'admin1.loans.my_clients',
        'icon'  => 'fa fa-users',
        'roles' => 'super admin|admin|processor|auditor|debt collector|loan approval|credit and processing',
    ],
    [
        'name'   => 'User Settings',
        'icon'   => 'fa fa-user',
        'roles'  => 'super admin',
        'values' => [
            [
                'name'  => 'Relationships',
                'route' => 'admin1.relationships.index',
                'roles' => 'super admin',
            ],
        ],
    ],
    [
        'name'   => 'Bundles Settings',
        'icon'   => 'fa fa-vcard',
        'roles'  => 'super admin',
        'values' => [
            [
                'name'  => 'Reasons',
                'route' => 'admin1.loan-reasons.index',
                'roles' => 'super admin',
            ],
            [
                'name'  => 'Decline reason',
                'route' => 'admin1.loan-decline-reasons.index',
                'roles' => 'super admin',
            ],
            [
                'name'  => 'Purchase Onhold reason',
                'route' => 'admin1.loan-onhold-reasons.index',
                'roles' => 'super admin',
            ],
            [
                'name'  => 'Types',
                'route' => 'admin1.loan-types.index',
                'roles' => 'super admin',
            ],
            [
                'name'  => 'Existing Loan Types',
                'route' => 'admin1.existing-loan-types.index',
                'roles' => 'super admin',
            ],
        ],
    ],
    [
        'name'   => 'Locations',
        'icon'   => 'fa fa-globe',
        'roles'  => 'super admin',
        'values' => [
            [
                'name'  => 'Country',
                'route' => 'admin1.countries.index',
                'roles' => 'super admin',
            ],
            [
                'name'  => 'District',
                'route' => 'admin1.districts.index',
                'roles' => 'super admin',
            ],
            [
                'name'  => 'Branch',
                'route' => 'admin1.branches.index',
                'roles' => 'super admin',
            ],
        ],
    ],
    [
        'name'   => 'Banks',
        'icon'   => 'fa fa-bank',
        'roles'  => 'super admin',
        'values' => [
            [
                'name'  => 'Manage',
                'route' => 'admin1.banks.index',
                'roles' => 'super admin',
            ],
            [
                'name'  => 'Reconcile',
                'route' => 'admin1.bank.reconcile',
                'roles' => 'super admin',
            ],
        ],
    ],
    [
        'name'   => 'Templates',
        'icon'   => 'fa fa-bank',
        'roles'  => 'super admin',
        'values' => [
            [
                'name'         => 'Emails',
                'route'        => 'admin1.templates.index',
                'query_string' => 'type=1',
                'roles'        => 'super admin',
            ],
            [
                'name'         => 'Push',
                'route'        => 'admin1.templates.index',
                'query_string' => 'type=2',
                'roles'        => 'super admin',
            ],
        ],
    ],
    [
        'name'  => 'Pay Bills',
        'route' => 'admin1.merchants.index',
        'icon'  => 'fa fa-money',
        'roles' => 'super admin',
    ],
    [
        'name'   => 'Merchants',
        'icon'   => 'fa fa-user',
        'roles'  => 'super admin|admin',
        'values' => [
            [
                'name'  => 'List',
                'route' => 'admin1.merchants1.index',
                'roles' => 'super admin|admin',
            ],
            [
                'name'  => 'Payments',
                'route' => 'admin1.merchants1.payments',
                'roles' => 'super admin|admin',
            ],
            [
                'name'  => 'Reconciliations',
                'route' => 'admin1.merchants1.reconciliations',
                'roles' => 'super admin|admin',
            ],
        ],
    ],
    [
        'name'   => 'Daily Turnovers',
        'icon'   => 'fa fa-calendar',
        'roles'  => 'super admin|admin|processor|auditor|credit and processing',
        'values' => [
            [
                'name'         => 'Audit Branch',
                'route'        => 'admin1.daily-turnovers.audit',
                'query_string' => 'type=1',
                'roles'        => 'super admin|auditor',
            ],
            [
                //'name'         => 'Audit Bank',
                //'route'        => 'admin1.daily-turnovers.audit',
                //'query_string' => 'type=2',
                //'roles'        => 'super admin|auditor'
                'name'  => 'Audit Bank',
                'route' => 'admin1.audit.reconcile',
                'roles' => 'super admin|auditor',
            ],
            [
                'name'         => 'Audit Report Vault',
                'route'        => 'admin1.daily-turnovers.audit',
                'query_string' => 'type=3',
                'roles'        => 'super admin|auditor',
            ],
            [
                'name'         => 'Day Open',
                'route'        => 'admin1.daily-turnover.day-open',
                'query_string' => 'type=1',
                'roles'        => 'super admin|admin|processor',
            ],
            [
                'name'         => 'Vault',
                'route'        => 'admin1.daily-turnover.day-open',
                'query_string' => 'type=3',
                'roles'        => 'super admin|admin|credit and processing',
            ],
            [
                'name'  => 'Correction',
                'route' => '',
                'roles' => 'super admin|admin',
            ],
        ],
    ],
    [
        'name'   => 'Credits',
        'icon'   => 'fa fa-dollar',
        'roles'  => 'super admin|admin|processor|credit and processing',
        'values' => [
            [
                'name'         => 'Transfer to bank-requests',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=1&type=2',
                'roles'        => 'super admin|admin|credit and processing',
            ],
            [
                'name'         => 'Transfer to bank-in process',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=2&type=2',
                'roles'        => 'super admin|admin|credit and processing',
            ],
            [
                'name'         => 'Transfer to bank-completed',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=3&type=2',
                'roles'        => 'super admin|admin|credit and processing',
            ],
            [
                'name'         => 'Transfer to bank-rejected',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=4&type=2',
                'roles'        => 'super admin|admin|credit and processing',
            ],
            [
                'name'         => 'Merchant payments',
                'route'        => '',
                'query_string' => '',
                'roles'        => 'super admin|admin|credit and processing',
            ],
            [
                'name'         => 'Cash payouts-requests',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=1&type=1',
                'roles'        => 'super admin|admin|processor',
            ],
            [
                'name'         => 'Cash payouts-approved',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=2&type=1',
                'roles'        => 'super admin|admin|credit and processing',
            ],
            [
                'name'         => 'Cash payouts-completed',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=3&type=1',
                'roles'        => 'super admin|admin|processor|credit and processing',
            ],
            [
                'name'         => 'Cash payouts-rejected',
                'route'        => 'admin1.credits.index',
                'query_string' => 'status=4&type=1',
                'roles'        => 'super admin|admin|credit and processing',
            ],
        ],
    ],
    [
        'name'   => 'Messages',
        'icon'   => 'fa fa-envelope-o',
        'roles'  => 'super admin|admin',
        'values' => [
            [
                'name'  => 'Push Notifications',
                'route' => 'admin1.push-notifications.index',
                'roles' => 'super admin|admin',
            ],
            [
                'name'  => 'SMS',
                'route' => 'admin1.messages.index',
                'roles' => 'super admin|admin',
            ],
        ],
    ],
    [
        'name'   => 'NLB',
        'icon'   => 'fa fa-dollar',
        'roles'  => 'super admin|admin|processor',
        'values' => [
            [
                'name'  => 'Transactions',
                'route' => 'admin1.nlbs.index',
                'roles' => 'super admin|admin|processor',
            ],
            [
                'name'  => 'Reasons',
                'route' => 'admin1.nlb-reasons.index',
                'roles' => 'super admin',
            ],
        ],
    ],
    [
        'name'   => 'Referral/Raffle',
        'icon'   => 'fa fa-vcard',
        'roles'  => 'super admin|admin|processor',
        'values' => [
            [
                'name'  => 'Referral Categories',
                'route' => 'admin1.referral-categories.index',
                'roles' => 'super admin|admin',
            ],
            [
                'name'  => 'Raffle Winners',
                'route' => 'admin1.raffle-winners.index',
                'roles' => 'super admin|admin',
            ],
            [
                'name'  => 'Referral History',
                'route' => 'admin1.referral-histories.index',
                'roles' => 'super admin|admin',
            ],
        ],
    ],
];