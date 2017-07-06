@extends('system.master')

@section('css')
    <link href="{{ asset('system/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/summernote/summernote.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/summernote/summernote-bs3.css')}}" rel="stylesheet">
    <link href="{{ asset('system/vendors/image-picker/image-picker.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/dropzone/basic.css')}}" rel="stylesheet">
    <link href="{{ asset('system/css/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
     <link href="{{ asset('system/css/plugins/cropper/cropper.min.css')}}" rel="stylesheet">
     <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
     <link href="{{ asset('system/css/plugins/datetimepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet">
@stop

@section('content')
<div class="col-lg-12">
	<div class="ibox float-e-margins">		
		<div class="ibox-content">
		@foreach($errors->all() as $error)
	        <p class="alert alert-danger">{{$error}}</p>
	    @endforeach
		{!! Form::open(array('url'=>$action,'method'=>$method,'onsubmit'=>'javascript:has_action=false','class'=>'form-horizontal','files'=>true))!!}			
			<?php
				$maps = array();
				$GLOBALS['maps'] = array();
			?>
			@foreach($properties as $property)				
				<?php 
					$property['page'] = 'form'; 
					$property['options_form'] = '';
					$property['name'] = $property->formPropertyLabels[0]->label;
				?>
				@include('system.renders.'.$property->DataType->name,$property)
			@endforeach
			<div class="form-group action-zone">
				<div class="col-sm-6 text-right">
					<a href="{{url('system/sites')}}" class="btn btn-white">Cancel</a>
					<button class="btn btn-primary" type="submit">Save</button>
				</div>
			</div>
		{!! Form::close() !!}
		</div>
	</div>
</div>
@stop

@section('script')
	<script src="{{ asset('system/vendors/image-picker/image-picker.js')}}"></script>
	<!-- DROPZONE -->
    <script src="{{ asset('system/js/plugins/dropzone/dropzone.js')}}"></script>
    <script>
        $(document).ready(function(){

            Dropzone.options.myAwesomeDropzone = {

                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 100,
                maxFiles: 100,

                // Dropzone settings
                init: function() {
                    myDropzone = this;

                    this.element.querySelector("button[type=submit]").addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        myDropzone.processQueue();
                    });
                    this.on("sendingmultiple", function() {
                    });
                    this.on("successmultiple", function(files, response) {
                    });
                    this.on("errormultiple", function(files, response) {
                    });
                }
            }
       });
    </script>
    <!-- DROPZONE -->
	<script>
	$(document).ready(function(){		
		$('.summernote').summernote({
			height: 500,
			onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable);
            }
		});
		function sendFile(file, editor, welEditable) {
            data = new FormData();
            data.append("file", file);//You can append as many data as you want. Check mozilla docs for this
            $_token = "{{ csrf_token() }}";
            data.append('_token',$_token);
            $.ajax({
                data: data,
                type: "POST",
                url: '{{url("system/uploadImagesAjax")}}',
                cache: false,
                contentType: false,
                processData: false,
                success: function(url) {
                	editor.insertImage(welEditable, '{{ asset("") }}'+url);
                	console.log('test');
                }
            });
        }
	});	
	</script>
	<script type="text/javascript"
	  src="https://maps.googleapis.com/maps/api/js">
	</script>
	<script type="text/javascript">
	  function initialize(lat,lng,id) {	  	
	    var mapOptions = {
	      center: { lat: lat, lng: lng},
	      zoom: 8
	    };
	    var map = new google.maps.Map(document.getElementById(id),
	        mapOptions);

	    var marker = new google.maps.Marker({
	    	position: new google.maps.LatLng(lat, lng),
		 	map: map, 
		  	title: 'test',
		  	draggable: true
		});
		google.maps.event.addListener(marker, 'drag', function(event) {
		    console.debug('new position is '+event.latLng.lat()+' / '+event.latLng.lng());
		});
		google.maps.event.addListener(marker, 'dragend', function(event) {
		    console.debug('final position is '+event.latLng.lat()+' / '+event.latLng.lng());	
		    console.debug(marker.map.streetView.V);	
		    my_map = marker.map.streetView.V;
		    my_map_id = $(my_map).attr('id');
		    gmnoprint = $('#'+my_map_id+' .gmnoprint').parents('.form-group');
		    $(gmnoprint).find('input.latitude').val(event.latLng.lat());
		    $(gmnoprint).find('input.longitude').val(event.latLng.lng());	
		});
	}
	@foreach($GLOBALS['maps'] as $index=>$map)
		initialize({{$map['latitude']}},{{$map['longitude']}},'map-canvas-{{$index+1}}');
	@endforeach
	<?php 
		unset($GLOBALS['maps']);
	?>
	</script>
@stop