<?php
if (isset($content_object)) {
    $renderLanguage = $language->name;
} else {
    $renderLanguage = $language;
}
if (isset($language) && isset($content_object)) {
    $map_id = 'map-canvas-' . $renderLanguage . '-';
} else {
    $map_id = 'map-canvas-';
}
?>
<div class="form-group googlemap">
    <div class="col-md-12">
        <label class="control-label">{{$lable or $name}}</label>
        <div id="{{$map_id}}{{sizeOf($GLOBALS['maps'])+1}}" style='height:300px;width:100%;'></div>
    </div>
    <?php
    $json_options = json_decode($options);
    if (isset($obj)) {
        $tmp = CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form);
        $tmp = json_decode($tmp, true);
    }
    $google_temp = array();
    ?>
    @foreach($json_options as $option)
        <?php
        if (isset($tmp)) {
            $google_temp[$option->name] = $tmp[$option->name];
        } else {
            $google_temp[$option->name] = $option->value[0];
        }
        ?>
        <div class="col-md-6">
            <label class="control-label">{{$option->name}}</label>
            <input type="text" name="{{$option->name}}[]" class="txt-lable form-control {{$option->name}}"
                   value="{{$google_temp[$option->name]}}">
        </div>
    @endforeach
    <?php
    $GLOBALS['maps'][] = $google_temp;
    ?>
    {{-- {{print_r($json_options)}} --}}
</div>