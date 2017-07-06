@extends('system.master')

@section('content')
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-title">
				<h5>All Menus</h5>&nbsp;&nbsp;<a href="{{url('system/menus/create/')}}"><span
							class='badge badge-primary'>Create</span></a>
				<div class="ibox-tools">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			<div class="ibox-content">
				<table id="table" class="display" cellspacing="0" width="100%">
					<thead>
					<tr>
						<th width="80">No</th>
						<th>Name</th>
						<th>Parent</th>
						<th>Group menu</th>
						<th class="col-md-1">Site</th>
						<th class="col-md-1">Active</th>
						<th class="col-md-1">Edit</th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
@stop

@section('script')
	<script>
		$(document).ready(function () {
			data_i = 1;
			var table = $('#table').DataTable({
				stateSave: true,
				"ajax": "{{URL::to('/system/menus/getAjax')}}",
				"columns": [
					{"data" : "id"},
					{"data": "menu_title"},
					{"data": "parent_menu_title","sClass":"text-center"},
					{"data": "menu_group","sClass":"text-center"},
					{"data": "site" ,"sClass":"text-center"},
					{"data": "active"},
				],
				"columnDefs": [
					{
						"sClass": "text-center",
						"data" : "id",
						"render": function (data, type, row) {
							return "<a href='{{url('system/menus/')}}/" + data + "/edit'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;<form action='{{url('system/menus')}}/" + data + "' method='post' class='inline-control form-ajax' onclick='return(delete_type(this))'><a class='delete-link'><i class='fa fa-trash-o'></i></a></form>"
						},
						"targets": 6
					},
					{
						"sClass": "text-center",
						"render": function (data, type, row) {
							if (data != 0) {
								return "<span class='badge badge-primary'><i class='fa fa-check'></i></span>";
							} else {
								return "<span class='badge badge-danger'><i class='fa fa-close'></i></span>";
							}
						},
						"targets": 5
					},
					{
						"sClass": "text-center",
						"render": function (data, type, row) {
							return data_i++;
						},
						"targets": 0
					},
				]
			});
		});
	</script>
@stop