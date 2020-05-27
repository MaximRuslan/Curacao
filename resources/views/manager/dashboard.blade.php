@extends('admin.layouts.app')

@section('content')
<div class="row">
	<div class="col-sm-12">
		<h4 class="page-title m-b-20">Manager Dashboard</h4>
	</div>
</div>

	@foreach($territory as $item)
	<div class="row">
		<div class="col-md-6 col-lg-6 col-xl-2">
			<div class="card-box widget-inline-box text-center">
				<h3><i class="text-warning fa fa-user"></i> <b data-plugin="counterup">{{$item->userCount}}</b></h3>
				<h4 class="text-muted font-16">Clients</h4>
			</div>
		</div>
		
		<div class="col-md-6 col-lg-6 col-xl-2">
			<div class="card-box widget-inline-box text-center">
				<h3><i class="text-warning fa fa-vcard"></i> <b data-plugin="counterup">{{$item->applicationsCount}}</b></h3>
				<h4 class="text-muted font-16">Applications</h4>
			</div>
		</div>
		<div class="col-md-6 col-lg-6 col-xl-3">
			<div class="card-box widget-inline-box text-center">
				<h3><i class="text-warning fa fa-bars"></i> <b data-plugin="counterup">{{$item->pendingCount}}</b></h3>
				<h4 class="text-muted font-16">Pending Applications</h4>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-lg-6 col-xl-3">
			<div class="card-box widget-box-1 bg-white">
				<h4 class="text-dark">Open application</h4>
				<h2 class="text-primary text-center"><span data-plugin="counterup">{{$item->openCount}}</span></h2>
				<p class="text-muted">Total balance: ${{$item->openBalance}}</p>
			</div>
		</div>
		<div class="col-md-6 col-lg-6 col-xl-3">
			<div class="card-box widget-box-1 bg-white">
				<h4 class="text-dark">Exceeding application</h4>
				<h2 class="text-primary text-center"><span data-plugin="counterup">{{$item->exceedingCount}}</span></h2>
				<p class="text-muted">Total balance: ${{$item->exceedingBalance}}</p>
			</div>
		</div>
	</div>
	@endforeach
@endsection