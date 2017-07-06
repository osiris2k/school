<div class="form-group">
		<label class="control-label">{{$lable or $name}}</label>
		<input type="text"  placeholder="{{$label or $name}}" name="{{$variable_name}}" class="txt-lable form-control" {{{ $is_mandatory==1 ? "required" : '' }}} 
		@if(isset($obj))
			value="{{\App\Helpers\CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}"
		@endif
		>
</div>