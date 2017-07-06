<div class="form-group">
	<div class="col-md-6">
		<label class="control-label">{{$lable or $name}}</label>
		<input type="email" placeholder="{{$label or $name}}" name="{{$variable_name}}" class="txt-lable form-control" {{{ $is_mandatory==1 ? "required" : '' }}} 
		@if(isset($obj))
			value="{{CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}"
		@endif
		>
	</div>
</div>