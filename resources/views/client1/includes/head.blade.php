<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<meta name="description" content=""/>
<meta name="keywords" content=""/>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link rel="shortcut icon" href="{!! \App\Library\Helper::siteFavicon() !!}"/>
<link href="{!! asset(mix('resources/css/client/style.css')) !!}" rel="stylesheet">
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