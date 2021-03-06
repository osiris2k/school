@extends('system.master')

@section('content')
<div class="col-lg-12">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>All Languages</h5>&nbsp;&nbsp;<a href="{{url('system/languages/create/')}}"><span class='badge badge-primary'>Create</span></a>
			<div class="ibox-tools">
				<a class="collapse-link">
					<i class="fa fa-chevron-up"></i>
				</a>
			</div>
		</div>
		<div class="ibox-content">
			{!! \Session::get('response') !!}
			<table id="table" class="display" cellspacing="0" width="100%">
		        <thead>
		            <tr>
		                <th width="80">No</th>	
		                <th>Language</th>		                	
		                <th>Edit</th>		                		
		            </tr>
		        </thead>
		    </table>			
		</div>
	</div>
</div>
@stop

@section('script')
	<script>
	$(document).ready(function(){		
		data_i = 1;		
	    var table = $('#table').DataTable({
	    	stateSave: true,
	        "ajax": "{{URL::to('/system/languages/getAjax')}}",
	        "columns": [
	            { "data": "id" },
	            { "data": "name" },		          
	            { "data": "id" },		          
	        ],	 
			"columnDefs": [
            {                
                "render": function ( data, type, row ) {
                    return "<a href='{{url('system/languages/')}}/"+data+"/edit'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;<form action='{{url('system/languages')}}/"+data+"' method='post' class='inline-control form-ajax' onclick='return(delete_type(this))'><a class='delete-link'><i class='fa fa-trash-o'></i></a></form>"
                },
                "targets": 2
            },
            {                
                "render": function ( data, type, row ) {                	
                    return data_i++;
                },
                "targets": 0,     
            }, 
        ]
    	});
	});
	</script>
@stop