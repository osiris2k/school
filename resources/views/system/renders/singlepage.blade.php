<?php
$value = -1;
if (isset($obj)) {
    $value = CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form);

    // $value_array = json_decode($value, true);--}}

}
$property = CmsHelper::getProperty($id);
$json = json_decode($property->options);
if ($json[0]->value[0] != "") {
    $object_name = $json[0]->value[0];
    $object_name = explode(',',$object_name);
    //$contents = CmsHelper::getPagesByContentObjectList($json[0]->value);
    $contents = \App\Helpers\CmsHelper::getPagesByContentObjectList($object_name);
} else {
    $contents = \App\Helpers\CmsHelper::getAllPages();
}

?>
<div class="form-group">
    <label class="control-label">{{$lable or $name}}</label>

    <div class="row">
        <div class="col-md-12">
            <select name="{{$variable_name}}[]" data-placeholder="Choose..." class="chosen-select col-xs-12"
                    {{ $is_mandatory==1 ? "required" : '' }}>
                <option value=''>Choose...</option>
                @foreach($contents as $content)
                    <option @if($content->id == $value) {{'selected'}} @endif value="{{$content->id}}">{{--$content->site->name--}}
                        ({{ucfirst(str_replace("_", "-", $content->template))}}) : {{$content->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>