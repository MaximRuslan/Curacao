@extends('admin.layouts.app')

@section('extra-styles')

<link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="row">
	<div class="col-sm-12">
		{{-- <div class="btn-group pull-right">
			<a data-toggle="modal" href="#userTypeModal" class="btn btn-default waves-effect waves-light">Add</a>
		</div> --}}
		<h4 class="page-title m-b-20">User Types</h4>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="card-box table-responsive"> 
			<table id="roles-table" class="table  table-striped table-bordered" style="width:100%">
				<thead>
					<tr>
						<th>Name ENG</th>
						<th>Name ESP</th>
						<th>Name NED</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

@include('common.delete_confirm',[
	'modalId'=>'deleteRole',
	'action'=>route('user-types.destroy','deleteId'),
	'item'=>'it',
	'callback'=>'showMsg'
	])

	@include('admin.usertype.create')
	@endsection	

	@section('extra-js')
	<script src="{{url(config('theme.admin.plugins'))}}/datatables/jquery.dataTables.min.js"></script>
	<script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.js"></script>

	<script src="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.responsive.min.js"></script>
	<script src="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.js"></script>
	@endsection

	@section('custom-js')
	<script type="text/javascript">
		var oTable = "";
		$(document).ready(function() {
			oTable = $('#roles-table').DataTable({
				processing: true,
				serverSide: true,
				ajax: {
					"url": "{{route('usertypes-data')}}",
					"type": "POST"
				},
				order: [],
				"drawCallback": function(settings) {
					InitTooltip();
				},
				columns: [
				{data: 'name',name: 'name'},
				{data: 'name_es',name: 'name_es'},
				{data: 'name_nl',name: 'name_nl'},
				{data: 'action', name: 'action',orderable:false, searchable: false},
				],
			});
			$('#userTypeModal').on('hidden.bs.modal', function () {
				$('#userTypeModal').find('form')[0].reset();
				$('#userTypeModal').find('form').find('input[name="id"]').val('');
				$('#userTypeModal').find('input').removeAttr('disabled');
				$('#userTypeModal').find('button[type="submit"]').show();
			})
		});
		function setEdit(id,type=''){
			var action = '{{route('user-types.show','')}}/'+id
			$.ajax({
				type: 'GET',
				url: action,
				data: {},
				dataType: 'json',
				success: function (data) {
					setFormValues('userType_form',data.inputs);
					$('#userTypeModal').modal('show');
					if(type=='view'){
						setTimeout(function(){							
							$('#userTypeModal').find('input').attr('disabled','disabled');
							$('#userTypeModal').find('button[type="submit"]').hide();
						},100);
					}
				},
				error: function (jqXHR, exception) {
				}
			});
		}
		function SaveUsertype(form){
			var action = $(form).attr('action')
			$.ajax({
				type: 'POST',
				url: action,
				data: $(form).serialize(),
				dataType: 'json',
				success: function (data) {
					successMsg('Message');
					$('#userTypeModal').modal('hide');
					oTable.draw(true);
				},
				error: function (jqXHR, exception) {
					var Response = jqXHR.responseText;
					ErrorBlock = $(form);
					Response = $.parseJSON(Response);
					DisplayErrorMessages(Response, ErrorBlock, 'input');
				}
			});
			return false;
		}
		function showMsg(){
			console.log('hello');
		}
	</script>
	@endsection
