<!DOCTYPE html>
<html lang="en">
@include('layouts.head')
<script type="text/javascript">
  var siteURL = "{!! config('app.url') !!}";
  var keywords = {!! json_encode(Lang::get('keywords')) !!};
  var lang = "{!! App::getlocale(); !!}";
</script>
<body data-spy="scroll" data-target="#navbar-menu">
@include('layouts.header_login')
{{-- @include('layouts.banner') --}}
<div>
@yield('content')
</div>
@include('layouts.footer')
@if (!Auth::guest())
    @include('common.profile_modal')
    @include('common.change_password_modal')
@endif
{{--<div class="loader"></div>--}}
@include('layouts.scripts')
</body>
</html>
