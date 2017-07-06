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
		<div class="panel-options">
            <ul class="nav nav-tabs">
            	@foreach($languages as $language)
                <li class="@if($language->initial==1) active @endif">
                	<a data-toggle="tab" href="#tab-{{$language->name}}">{{strtoupper($language->name)}}</a>
                </li>
                @endforeach                
            </ul>
        </div>		
		<div class="ibox-content">
			<div class="panel-body">
	            <div class="tab-content">
	            	@foreach($languages as $language)
	                <div id="tab-{{$language->name}}" class="tab-pane @if($language->initial==1) active @endif">
	                	{!! Form::open(array('url'=>$action,'method'=>$method,'class'=>'form-horizontal','files'=>true))!!}
								<input type="hidden" name="language_id" value="<?php echo $language->id?>">		
								<input type="hidden" name="form_object_id" value="<?php echo $form_object->id?>">		                		
		                	@foreach($properties as $property)	
		                		<?php 
		                			$obj = App\Helpers\CmsHelper::getLabelValue($language->id,$property->id);		
		                		?>
		                		<div class="form-group">
		                			<div class="col-xs-6">
									<label class="control-label">{{$property->name}}</label>
									<input type="text" name="label-{{$property->id}}" 
									value="@if($obj){{$obj->label}}@endif" class="form-control">
									</div>
									<div class="col-xs-6">
									<label class="control-label">Tooltip {{$property->name}}</label>
									<textarea name="tooltip-{{$property->id}}" class="form-control" rows="2">@if($obj){{$obj->tooltip}}@endif</textarea>
									</div>
								</div>	
		                	@endforeach
		                    <div class="action-zone">
								<div class="text-right">
									{{-- <a href="{{url('system/forms/'.$form_object->
									id.'-'.$form_object->name)}}" class="btn btn-white">Cancel</a> --}}
									<a href="{{url('system/form-objects')}}" class="btn btn-white">Cancel</a>
									<button class="btn btn-primary" type="submit">Save</button>
								</div>
							</div>
						{!! Form::close(); !!}
	                </div>
	                @endforeach	                
	            </div>
	        </div>
		</div>
	</div>
</div>
@stop

@section('script')
	
@stop