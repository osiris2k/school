<div class="form-group">
	<label class="control-label">{{$lable or $name}}</label>
	<textarea placeholder="{{$label or $name}}" name="{{$variable_name}}" class="form-control" cols="30" rows="10" {{{ $is_mandatory==1 ? "required" : '' }}} title = "{{$label_tooltip or ''}}">@if(isset($obj)){{\App\Helpers\CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}@endif</textarea>
</div>