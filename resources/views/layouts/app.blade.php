<!DOCTYPE html>
<html lang="en">
@include('layouts.head')
<script type="text/javascript">
    var siteURL = "{!! config('app.url') !!}";
    var keywords = {!! json_encode(Lang::get('keywords')) !!};
    var lang = "{!! App::getlocale(); !!}";
    var dateFormat = "{!! config('site.date_format.js') !!}";
    var isAdmin = "{!! (auth()->user() && auth()->user()->hasAnyRole(['admin','super admin'])) ? 'true' : 'false' !!}";
</script>
<body data-spy="scroll" data-target="#navbar-menu" class="@yield('body_class')">
@if(auth()->check())
    @include('layouts.header')
@endif
{{-- @include('layouts.banner') --}}
@yield('content')
@include('layouts.footer')
@if (!Auth::guest())
    @include('common.profile_modal')
    @include('common.change_password_modal')
@endif
{{--<div class="loader"></div>--}}
@include('layouts.scripts')
</body>
</html>
