@if (count($errors) > 0)
<div class="alert alert-danger">
	<ul class="list-group">
		@foreach ($errors->all() as $key=>$error)
		@if($error!='')
			<li class="{!! $key !!} {{($key=='checkout-error' ? "font-16" : "")}}">{!! $error !!}</li>
		@endif
		@endforeach
	</ul>
</div>
@endif