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
			{!! Form::open(array('url'=>$action,'method'=>$method,'class'=>'form-horizontal','files'=>true))!!}
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Site</label>
						<select class="form-control m-b" name="site_id">
							<option value="0">- All -</option>
							@foreach($sites as $site)
								<?php
									$selected = '';
									if(isset($obj))
									{
										if($obj->site_id == $site->id)
										{
											$selected = 'selected';
										}
									}
								?>
								<option value="{{$site->id}}" {{$selected}}>{{$site->name}}</option>
							@endforeach
				        </select>
					</div>
				</div>	
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Redirect Type</label>
						<select class="form-control m-b" name="type">
							<option value="302" @if(!empty($obj) && $obj->type == 302) {{'selected'}} @endif>Temporary</option>
							<option value="301" @if(!empty($obj) && $obj->type == 301) {{'selected'}} @endif>Permanently</option>
				        </select>
					</div>
				</div>	
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Source URL</label>
						<input type="text" name="source_url" class="txt-lable form-control" value="{{ $obj->source_url or old('source_url') }}">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Destination URL</label>
						<input type="text" name="destination_url" class="txt-lable form-control" value="{{ $obj->destination_url or old('destination_url') }}">
					</div>
				</div>										
				<div class="form-group action-zone">
					<div class="col-sm-6 text-right">
						<a href="{{url('system/redirecturls/')}}" class="btn btn-white">Cancel</a>
						<button class="btn btn-primary" type="submit">Save</button>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop