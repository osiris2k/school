<div class="form-group">	
		<label class="control-label">{{$lable or $name}}</label>
		<input  placeholder="{{$label or $name}}" type="text" name="{{$variable_name}}" class="txt-lable form-control date-picker" {{{ $is_mandatory==1 ? "required" : '' }}} 
		@if(isset($obj))
			value="{{CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}"
		@endif
		>
</div>