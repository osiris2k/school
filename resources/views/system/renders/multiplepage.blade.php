<?php
$value_array = [];
if (isset($obj)) {
    $value = CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form);
    $value_array = json_decode($value, true);
}
$property = CmsHelper::getProperty($id);
$json = json_decode($property->options);
if ($json[0]->value[0] != "") {
    $list = $json[0]->value[0];
    $list = explode(',', $list);
    $contents = CmsHelper::getPagesByContentObjectList($list);
} else {
    $contents = CmsHelper::getAllPages();
}
if ($value_array == null) {
    $value_array = [];
} elseif (!is_array($value_array)) {
    $value_array = Array($value_array);
}
// Remove 'null' value
$value_array = array_values(array_filter($value_array));
?>
@section('custom_script')
    <script>
        // TODO Clean code
        $(document).ready(function () {
            var sortAbleEle;
            var multipleSelectedIdArray;
            var selectedMultiple;
            var chosenContainer;
            var inputSortAbleId;

            selectedMultiple = $('#{{$variable_name.'_'.$language->name}}');
            inputSortAbleId = $('#{{$variable_name}}_mutiple_sortable_id_{{$language->name}}');
            chosenContainer = $(selectedMultiple).siblings('.chosen-container');
            multipleSelectedIdArray = [];
            sortAbleEle = chosenContainer.find('.chosen-choices');

            $(selectedMultiple).setSelectionOrder({!!json_encode($value_array) !!});

            $(selectedMultiple).on('change', updatedSelected);

            $(sortAbleEle).on("sortupdate", updatedSelected);

            function updatedSelected (event, ui) {
                multipleSelectedIdArray = [];

                setTimeout(function(){
                    $(sortAbleEle).children('li:not(.search-field)').each(function (index, ele) {
                        var selectIndex = $(ele).children('a').attr('data-option-array-index');
                        var reSortMultipleSelectedArray = $(selectedMultiple).children('option:eq(' + selectIndex + ')').val();
                        multipleSelectedIdArray.push(reSortMultipleSelectedArray);
                    });

                    console.log(multipleSelectedIdArray);
                    inputSortAbleId.val(JSON.stringify(multipleSelectedIdArray));
                }, 500);

            }
        });

    </script>
@append

<div class="form-group">
    <label class="control-label">{{$lable or $name}}</label>

    <div class="row">
        <div class="col-md-12">
            <input type="hidden" id="{{$variable_name}}_mutiple_sortable_id_{{$language->name}}"
                   name="{{$variable_name}}[]"
                   value="{{json_encode($value_array)}}">
            <select name="{{$variable_name}}_mutiple_sortable_id_{{$language->name}}" data-placeholder="Choose..."
                    id="{{$variable_name.'_'.$language->name}}"
                    class="chosen-select-width chosen-sortable col-xs-12"
                    multiple {{ $is_mandatory==1 ? "required" : '' }}>
                @foreach($contents as $content)
                    @if(in_array($content->id, $value_array))
                        <option selected value="{{$content->id}}">{{$content->site->name}}
                            : {{$content->name}}</option>
                    @else
                        <option value="{{$content->id}}">{{$content->site->name}}: {{$content->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
</div>
