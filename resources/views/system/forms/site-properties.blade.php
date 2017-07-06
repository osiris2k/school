@extends('system.master')

@section('content')
<div class="col-lg-12">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>{{$title}}</h5>
		</div>
		<div class="ibox-content">
			@foreach($errors->all() as $error)
		    	<p class="alert alert-danger">{{$error}}</p>
		    @endforeach
			<div class="form-group">
				<label class="col-sm-2 control-label">Select type</label>
				<div class="col-sm-6">
					<select class="form-control m-b text-uppercase" name="type" id='type'>
						<option value="">Choose type</option>
						@foreach ($dataTypes as $dataType)
							<option value="{{ $dataType->id }}">{{ $dataType->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="btn btn-primary" id="add-type">Add</div>
			</div>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
	<div id="new-type">

	</div>
	<div id="box-type">
		@foreach($site_properties as $site_property)
		<?php 
			$json_options = json_decode($site_property->options);
		?>
		<div class="ibox float-e-margins" data-type='site' data-id="{{$site_property->id}}">
			<div class="ibox-title text-uppercase">
				<i class="fa fa-font"></i><span class='show-label'>{{$site_property->name}}</span> <span class="label label-default">{{$site_property->dataType->name}} </span>
					{!! Form::open(array('url'=>'system/site-properties/'.$site_property->id,'method'=>'post','class'=>'inline-control form-ajax','onclick'=>'return(delete_type(this))'))!!}
						<input type="hidden" name="_method" value="DELETE">
						<a class="delete-link">
							<i class="fa fa-trash-o"></i>
						</a>
					{!! Form::close() !!}
				<div class="ibox-tools">
					<a class="collapse-link fl">
						<i class="fa fa-chevron-up"></i>
					</a>
				</div>
			</div>
			{!! Form::open(array('url'=>'system/site-properties/'.$site_property->id,'method'=>'put'))!!}
			<div class="ibox-content">
				<div class="form-group">
					<div class="col-md-6">
						<input type="hidden" name="data_type_id" value="{{$site_property->data_type_id}}">
						<label class="control-label">Label</label>
						<input type="text" name="label" value="{{$site_property->name}}" class="txt-lable form-control" required onkeyup="setLable(this)">
					</div>
					<div class="col-md-6">
						<label class="control-label">Variable Name</label>
						<input type="text" name="var" class="form-control" value="{{$site_property->variable_name}}" required>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">
						<input type="radio" name="is_mandatory" value="1" {{{ $site_property->is_mandatory==1 ? "checked" : '' }}}>
						Mandatory</label>
						<label class="control-label">
						<input type="radio" name="is_mandatory" value="0" {{{ $site_property->is_mandatory==0 ? "checked" : '' }}}>
						Optional</label>
					</div>
				</div>
				@foreach ($json_options as $json_option)
				<div class="form-group">	
					<div class="col-md-12">
						<label class="control-label">{{$json_option->name}}</label>
						<?php
							$tmp = $json_option->value[0];
						?>
						<input type="text" class="form-control" name="{{$json_option->name}}" 
						value="{{$tmp}}">
					</div>
				</div>
				@endforeach
				<div class="form-group">
					<div class="col-md-12" style="margin-top:20px;">		<input type="submit" class="btn btn-primary" value="Save">
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
		@endforeach
	</div>
</div>
@stop

@section('script')
<script src="{{ asset('system/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{ asset('system/js/plugins/dropzone/dropzone.js')}}"></script>
<script>
	$(document).ready(function(){
		WinMove();
		$("#add-type").click(function(){
			var type_name = $('#type option:selected').text();
			var type_id   = $('#type option:selected').val();
			renderType(type_id,type_name);
		});
		function renderType(type_id,type_name)
		{
			url = '{{ action('System\SystemController@renderType','', $attributes = array(), $secure = null) }}/'+type_id+'/'+type_name+'/site';
			$.get( url, function( data ) {
				$('#new-type').append(data);
				$('#new-type .ibox').fadeIn();
			});
		}
	});
</script>
@stop