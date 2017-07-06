<?php
// Set $wysiwyg for separate language tab
if (isset($content_object)) {
    $wysiwygId = $variable_name . '_' . $language->name;
    $renderLanguage = $language->name;
} else {
    $wysiwygId = $variable_name . '_' . $language;
    $renderLanguage = $language;
}
?>
<div class="form-group">

    <label class="control-label">{{$lable or $name}}</label>
    <textarea name="{{$variable_name}}" id="richtext-{{$wysiwygId}}"
              class="new_richtext_editor">@if(isset($obj)){{CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}@endif</textarea>
    <div class="clearfix"></div>
    <div class="hr-line-dashed"></div>
</div>
