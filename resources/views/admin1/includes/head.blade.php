<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<link rel="stylesheet" href="{!! asset(mix('resources/css/admin/style.css')) !!}">
<link rel="shortcut icon" href="{!! \App\Library\Helper::siteFavicon() !!}"/>
<style>
    .loader {
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .table-user tbody td {
        border-color: black;
    }

    .table-user thead th {
        border-color: black;
        border-bottom: 1px solid black !important;
    }

    .wizard > .content > .body input[type="radio"] {
        border: none !important;
    }

    .country_code_label {
        background-color: ghostwhite;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }

    input.error {
        display: block !important;
        border: 1px solid #ccc !important;
        background-color: #ffffff !important;
    }

    @if(env('APP_ENV')=='Test')
    #sidebar-menu > ul > li > a.active {
        background: #e4a8a8 !important;
    }

    .topbar .topbar-left {
        background-color: #ff0505 !important;
    }
    @endif
</style>
<style>
    /* For Firefox */
    input[type='number'] {
        -moz-appearance:textfield;
    }
    /* Webkit browsers like Safari and Chrome */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>