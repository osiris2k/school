<div class="form-group">
		<label class="control-label">{{$label or $name}}</label>
		<input type="text" placeholder="{{$label or $name}}" name="{{$variable_name}}" class="txt-lable form-control" {{ $is_mandatory==1 ? "required" : '' }}
		@if(isset($obj))
			value="{{CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}"
		@endif
		title = "{{$label_tooltip or ''}}"
		>
</div>