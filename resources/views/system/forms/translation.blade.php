@extends('system.master')

@section('content')
<?php
	if(isset($obj))
	{
		$type = $obj->type;
	}else{
		$type = '';
	}
?>
<div class="col-lg-12">
	<div class="ibox float-e-margins">
		<div class="ibox-content">
			<div class="form-horizontal">
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Key</label>
						<input type="text" name="key" value="{{ $obj->key or old('key') }}" class="txt-lable translation_key_action form-control" required>
					</div>					
				</div>
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Type</label>					
						<select name="type" class="form-control translation_type_action" required>			
							{{-- <option value="">Choose</option> --}}
							<option value="text" @if($type=='text') {{'selected'}} @endif>Text</option>
							{{-- <option value="richtext" @if($type=='richtext') {{'selected'}} @endif>Richtext</option> --}}
						</select>
					</div>					
				</div>	
			</div>
		</div>
	</div>
</div>

<div class="col-lg-12">
	<div class="ibox float-e-margins">
		<div class="panel-options">
            <ul class="nav nav-tabs">
            	@foreach($languages as $language)
                <li class="@if($language->initial==1) active @endif">
                	<a data-toggle="tab" href="#tab-{{$language->name}}" onclick="showMap('{{$language->name}}')">{{strtoupper($language->name)}}</a>
                </li>
                @endforeach                
            </ul>
        </div>	
		<div class="ibox-content">
			<div class="panel-body">
	        	<div class="tab-content">	        		
					@foreach($errors->all() as $error)
				        <p class="alert alert-danger">{{$error}}</p>
				    @endforeach		
					@foreach($languages as $language)	
					 <div id="tab-{{$language->name}}" class="tab-pane @if($language->initial==1) active @endif">
			            {!! Form::open(array('url'=>$action,'method'=>$method,'onsubmit'=>'return(SubmitContent())','class'=>'form-horizontal','files'=>true))!!}
							<input type="hidden" name='language_id' value="{{$language->id}}">	
							<input type="hidden" name='key' class='translation_key' value="">				
							<input type="hidden" name='type' class='translation_type'value="">				

							<div class="form-group">
								<div class="col-md-6">
									<label class="control-label">Value</label>
									<input type="text" name="value" value="{{$value[$language->name] or old('key') }}" class="txt-lable form-control" required>
								</div>					
							</div>
							<div class="form-group action-zone">
								<div class="col-sm-6 text-right">
									<a href="{{url('system/translations')}}" class="btn btn-white">Cancel</a>
									<button class="btn btn-primary" type="submit">Save</button>
								</div>
							</div>
						{!! Form::close()!!}
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('script')
<script>
	function SubmitContent()
	{
		$('.translation_key').val($('.translation_key_action').val());
		$('.translation_type').val($('.translation_type_action').val());
		setTimeout(function() {	     	
			return true;
	    }, 3000);		
	}
</script>
@stop