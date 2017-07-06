<label class="control-label">{{$lable or $name}}</label>
<textarea name="{{$variable_name}}" id="{{$variable_name.'_'.$language->name}}"
          class="new_richtext_editor">@if(isset($obj)){{CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}@endif</textarea>
<div class="clearfix"></div>
<div class="hr-line-dashed"></div>
