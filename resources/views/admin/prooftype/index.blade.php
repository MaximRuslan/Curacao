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
			<a data-toggle="modal" href="#proofTypeModal" class="btn btn-default waves-effect waves-light">Add</a>
		</div>
		<h4 class="page-title">Proof Types</h4>
	</div>
</div>
<div class="row">
	<div class="col-12">
		<div class="card-box table-responsive"> 
			<table id="proof-table" class="table  table-striped table-bordered" style="width:100%">
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
	'modalId'=>'deleteProof',
	'action'=>route('proof-types.destroy','deleteId'),
	'item'=>'it',
	'callback'=>'showMsg'
	])

	@include('admin.prooftype.create')
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
			oTable = $('#proof-table').DataTable({
				processing: true,
				serverSide: true,
				ajax: {
					"url": "{{route('proof-type-data')}}",
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
			$('#proofTypeModal').on('hidden.bs.modal', function () {
				$('#proofTypeModal').find('form')[0].reset();
				$('#proofTypeModal').find('form').find('input[name="id"]').val('');
				$('#proofTypeModal').find('input').removeAttr('disabled');
				$('#proofTypeModal').find('button[type="submit"]').show();
			})
		});
		function setEdit(id,type=''){
			var action = '{{route('proof-types.show','')}}/'+id
			$.ajax({
				type: 'GET',
				url: action,
				data: {},
				dataType: 'json',
				success: function (data) {
					setFormValues('proofType_form',data.inputs);
					$('#proofTypeModal').modal('show');
					if(type=='view'){
						setTimeout(function(){							
							$('#proofTypeModal').find('input').attr('disabled','disabled');
							$('#proofTypeModal').find('button[type="submit"]').hide();
						},100);
					}
				},
				error: function (jqXHR, exception) {
				}
			});
		}
		function SaveProofType(form){
			var action = $(form).attr('action')
			$.ajax({
				type: 'POST',
				url: action,
				data: $(form).serialize(),
				dataType: 'json',
				success: function (data) {
					successMsg('Message');
					$('#proofTypeModal').modal('hide');
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
