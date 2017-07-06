<div class="ibox float-e-margins">
	<div class="ibox-title text-uppercase">
			<i class=""></i><span class='show-label'></span> <span class='label label-default'>({{$data_type}})</span>
	</div>
	<div class="ibox-content">
		{!! Form::open(array('url'=>$url,'method'=>'post','class'=>'form-horizontal','onsubmit'=>'return(form_ajax(this))'))!!}
			<div class="form-group">
				<div class="col-md-6">
					<input type="hidden" name="data_type_id" value="{{$data_type_id}}">
					<label class="control-label">Label</label>
					<input type="text" name="label" class="txt-lable form-control" required onkeyup="setLable(this)">
				</div>
				<div class="col-md-6">
					<label class="control-label">Variable Name</label>
					<input type="text" name="var" class="form-control variable" required>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="control-label">
					<input type="radio" name="is_mandatory" value="1"> Mandatory </label>  
					 <label>
					 <input type="radio" checked name="is_mandatory" value="0"> Optional</label>
				</div>
			</div>
		@foreach ($data_type_options as $data_type_option)	
		<div class="form-group">
			<div class="col-md-12">
					<label class="control-label">{{$data_type_option->name}}</label>
					<input type="text" class="form-control" name="{{$data_type_option->name}}[]" 
					value="{{$data_type_option->default}}"
					@if($data_type_option->required==1)
						{{'required'}}
					@endif
					>
			</div>
		</div>
		@endforeach
		<div class="form-group">
			<div class="col-md-12" style="margin-top:20px;">
				<div class="btn btn-white" onclick="deleteType(this)">Cancel</div>
				<input type="submit" class="btn btn-primary" value="Save">
			</div>
		</div>
		{!! Form::close() !!}
	</div>
</div>