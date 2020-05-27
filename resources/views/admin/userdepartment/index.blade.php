@extends('admin.layouts.app')

@section('extra-styles')

<link href="{{url(config('theme.admin.plugins'))}}/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="{{url(config('theme.admin.plugins'))}}/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="{{url(config('theme.admin.plugins'))}}/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
@endsection


@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="btn-group pull-right m-b-20">
			<a data-toggle="modal" href="#userDepartmentModal" class="btn btn-default waves-effect waves-light">Add</a>
		</div>
		<h4 class="page-title">User department</h4>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="card-box table-responsive"> 
			<table id="department-table" class="table  table-striped table-bordered" style="width:100%">
				<thead>
					<tr>
						<th>Title ENG</th>
						<th>Title ESP</th>
						<th>Title NED</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

@include('common.delete_confirm',[
	'modalId'=>'deleteDepartment',
	'action'=>route('user-department.destroy','deleteId'),
	'item'=>'it',
	'callback'=>'showMsg'
	])

	@include('admin.userdepartment.create')
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
			oTable = $('#department-table').DataTable({
				processing: true,
				serverSide: true,
				ajax: {
					"url": "{{route('userdepartment-data')}}",
					"type": "POST"
				},
				order: [],
				"drawCallback": function(settings) {
					InitTooltip();
				},
				columns: [
				{data: 'title',name: 'title'},
				{data: 'title_es',name: 'title_es'},
				{data: 'title_nl',name: 'title_nl'},
				{data: 'action', name: 'action',orderable:false, searchable: false},
				],
			});
			$('#userDepartmentModal').on('hidden.bs.modal', function () {
				$('#userDepartmentModal').find('form')[0].reset();
				$('#userDepartmentModal').find('form').find('input[name="id"]').val('');
				$('#userDepartmentModal').find('input').removeAttr('disabled');
				$('#userDepartmentModal').find('button[type="submit"]').show();
			})
		});
		function setEdit(id,type=''){
			var action = '{{route('user-department.show','')}}/'+id
			$.ajax({
				type: 'GET',
				url: action,
				data: {},
				dataType: 'json',
				success: function (data) {
					setFormValues('userDepartment_form',data.inputs);
					$('#userDepartmentModal').modal('show');
					if(type=='view'){
						setTimeout(function(){							
							$('#userDepartmentModal').find('input').attr('disabled','disabled');
							$('#userDepartmentModal').find('button[type="submit"]').hide();
						},100);
					}
				},
				error: function (jqXHR, exception) {
				}
			});
		}
		function SaveUserdepartment(form){
			var action = $(form).attr('action')
			$.ajax({
				type: 'POST',
				url: action,
				data: $(form).serialize(),
				dataType: 'json',
				success: function (data) {
					successMsg('Message');
					$('#userDepartmentModal').modal('hide');
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
