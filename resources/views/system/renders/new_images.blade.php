<?php

if (isset($obj)) {
    $values = json_decode(CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form));
    $hidden = CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form);
    $hidden = json_decode($hidden, true);
    $tmp_hidden = array();
    if ($hidden == null) {
        $hidden = [];
    }
    foreach ($hidden as $hidden_obj) {
        $tmp_hidden[] = $hidden_obj['image'];
    }
    $hidden = json_encode($tmp_hidden);
}

$options = json_decode($options);
$allowExt = '';
$height = "*";
$width = "*";

if (!empty($options)) {

    foreach ($options as $oItem) {
        if ($oItem->name == 'mimes') {
            //Set cms allow upload file
            $allowExt = implode(',', $oItem->value);
        }
    }

    $width = array_shift($options);
    $width = (!empty($width->value[0])) ? $width->value[0] : '*';

    $height = array_shift($options);
    $height = (!empty($height->value[0])) ? $height->value[0] : '*';
}

// Set uniqueId for separate language tab
echo $uniqueId = $variable_name . '_' . $language->name;
?>
@section('custom_script')
    <script>
        // TODO check value missing after change folder (use Session)(not use now)
        // TODO clean code
        // TODO maybe add force to crop later
        // TODO add restore delete button and function
        var uniqueId = '{{$uniqueId}}';
        var cmsLang = '{{$language->name}}';
        var variableName = '{{$variable_name}}';
        var assetPath = '{{asset('')}}';
        var propertyName = '{{$variable_name}}';
        var allowExt = '{{$allowExt}}';
        var languageName = '';

        $(document).ready(function () {

            // Init New Images orderable
            newImagesOrderAble();

            $('.new_images_close_modal').click(function () {
                var newImageIframeDocument = $('#new_images_iframe_' + uniqueId).contents();
                clearSelectedFile(newImageIframeDocument);
            });

            $(".content_tab_language").click(function () {
                setConfig($(this).attr('data-langauge'));
            })

            $('.bt_open_news_images_modal').click(function () {
                propertyName = variableName = $(this).attr('data-variable_name');

                setConfig($(this).attr('data-cms_lang'));
                renderNewImagesIframe(uniqueId);
            });

            $('.bt_save_news_images').click(function () {
                uniqueId = $(this).attr('data-unique_id');
                propertyName = $(this).attr('data-property_name');
                variableName = $(this).attr('data-property_name');
                languageName = $(this).attr('data-language_name');

                var newImageIframeModal = $('#new_images_modal_' + uniqueId);
                var newImageIframeDocument = $('#new_images_iframe_' + uniqueId).contents();
                var selectedFileVal = newImageIframeDocument.find('#multiple_file_value').val();

                if (selectedFileVal.length > 0) {
                    selectedFileVal = JSON.parse(selectedFileVal);
//                    selectedFileVal = checkNewImagesData(selectedFileVal);
                    storeNewImages(selectedFileVal);
                    renderNewImages(selectedFileVal);
                    newImagesOrderAble();
                } else {
                    newImageIframeModal.modal('hide');
                }

                // Clear selected file
                clearSelectedFile(newImageIframeDocument);
            });
        });

        function setConfig(lang) {
            uniqueId = variableName + '_' + lang;
            cmsLang = lang;
        }

        function renderNewImagesIframe(uniqueId) {
            var iframeEle;
            var newImagesModalBody = $('#modal_body_' + uniqueId);
            var newImagesIframe = newImagesModalBody.children('iframe');

            if (newImagesIframe.length == 0) {
                iframeEle = '<iframe id="new_images_iframe_' + uniqueId + '" class="new_images_iframe" width="100%" height="550"'
                        + 'frameborder="0"'
                        + 'src="{{ route('files_manager.system.get_file_manager_lib') }}?type=1&field_id=file_input&multiple_file=TRUE&cms_lang=' + cmsLang + '&variable_name=' + variableName + '&override_allow_extension=' + allowExt + '">'
                        + '</iframe>';
                $('#modal_body_' + uniqueId).html(iframeEle);
            }

            // Not use now
            //syncSelectedFileInIframe();
        }

        /**
         * This method not use now its use for user can't select duplicate file
         */
        // TODO if have selected images should sync with value in iframe
        // TODO if delete images should remove selected file style in iframe
        function syncSelectedFileInIframe() {
            var newImagesData = getStoreNewImages();
            var newImageIframeDocument = $('#new_images_iframe_' + uniqueId).contents();
            newImageIframeDocument.find('#multiple_file_value').val(newImagesData);
        }

        function clearSelectedFile(iframe) {
            iframe.find('img').attr('style', 'border:0;');
            iframe.find('#multiple_file_value').val('');
        }

        /**
         * Not use now
         */
        function checkNewImagesData(newImagesData) {
            var newImagesStore = $('#store_new_image_' + uniqueId);
            var newValToStore;

            if (newImagesStore.val().length > 0) {
                newValToStore = JSON.parse(newImagesStore.val());
                newImagesData = newValToStore.concat(newImagesData);
            }

            return newImagesData;
        }

        function storeNewImages(newImagesData, storeType) {
            var newImagesStore = $('#store_new_image_' + uniqueId);
            console.log('storeNewImages', uniqueId, newImagesStore.val(), typeof newImagesStore.val());
            if (newImagesStore.val().length > 0) {
                var newValToStore = JSON.parse(newImagesStore.val());
                if (storeType === undefined) {
                    newImagesData = newValToStore.concat(newImagesData);
                }
            }

            newImagesStore.val(JSON.stringify(newImagesData));
            $('#textarea_' + uniqueId).val(JSON.stringify(newImagesData));
        }

        /**
         * Not use now
         */
        function getStoreNewImages() {
            var newImagesStore = $('#store_new_image_' + uniqueId);
            return $('#textarea_' + uniqueId).val();
            // TODO after test use below return value
            return newImagesStore.val();
        }

        function renderNewImages(newImagesData) {
            var newImageDisplayContainer = $('#display_new_images_section_' + uniqueId);
            var newImageIframeModal = $('#new_images_modal_' + uniqueId);
            var appendData = '';
            var childIndex = findNewImagesItemIndex();
            $.each(newImagesData, function (index, val) {
                appendData += newImagesFormat(childIndex, val);
                childIndex++;
            });

            newImageDisplayContainer.append(appendData);
            newImageIframeModal.modal('hide');
        }

        function findNewImagesItemIndex() {
            var newImageDisplayContainer = $('#display_new_images_section_' + uniqueId);

            return newImageDisplayContainer.children('.new_images_order_item:not(.waiting_to_del)').length;
        }

        function newImagesFormat(index, fileData) {
            var appendData = '<div class="col-md-12 cursor_sortable new_images_order_item" id="new_images_display_container_' + uniqueId + '_' + index + '" data-unique_id="' + uniqueId + '" data-cms_lang="' + languageName + '" data-variable_name="' + variableName + '">'; // Begin container
            appendData += '<div class="col-md-2">'; // Begin Image thumb container
            appendData += '<img src="' + assetPath + fileData + '" class="img-responsive" data-new_images_src="' + fileData + '">';
            appendData += '<button type="button" class="btn btn-block btn-danger btn-xs " data-new_images_src="' + fileData + '" data-index="' + index + '" data-unique_id="' + uniqueId + '" data-cms_lang="' + languageName + '" data-variable_name="' + variableName + '" onclick="deleteNewImages(this)">Delete</button>';
            appendData += '</div>'; // End Image thumb container
            appendData += '<div class="col-md-10">'; // Begin Image properties
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Title</label><input type="text" class="form-control" name="' + propertyName + '_title[]" value="">';
            appendData += '</div>';
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Alt</label><input type="text" class="form-control" name="' + propertyName + '_alt[]" value="">';
            appendData += '</div>';
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Caption</label><input type="text" class="form-control" name="' + propertyName + '_caption[]" value="">';
            appendData += '</div>';
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Order</label><input type="text" class="form-control new_images_order" name="' + propertyName + '_order[]" value="' + (index + 1) + '">';
            appendData += '</div>';
            appendData += '</div>'; // End Image properties
            appendData += '<div class="clearfix"></div>';
            appendData += '<div class="hr-line-dashed"></div>';
            appendData += '</div>'; // End container

            return appendData;
        }

        function deleteNewImages(deleteEle) {
            uniqueId = $(deleteEle).attr('data-unique_id');
            var removeVal = $(deleteEle).attr('data-new_images_src');
            var removeContainerIndex = $(deleteEle).attr('data-index');
            var removeIndexData = 0;
            var newImagesStore = $('#store_new_image_' + uniqueId);
            console.log('deleteNewImages', typeof newImagesStore.val(), newImagesStore.val());
            var newValToStore = JSON.parse(newImagesStore.val());
            var deleteParentContainer = $(deleteEle).closest('.news_images_order_able');

            removeIndexData = newValToStore.indexOf(removeVal);
            newValToStore.splice(removeIndexData, 1);
//            $('#new_images_display_container_' + uniqueId + '_' + removeContainerIndex).remove();
            $('#new_images_display_container_' + uniqueId + '_' + removeContainerIndex).addClass('waiting_to_del');

            reOrderNewImages(deleteParentContainer);
        }

        function reOrderNewImages(deleteParent) {
            var newImagesOrderArray = [];

            $.each(deleteParent.children('.new_images_order_item:not(.waiting_to_del)'), function (index, ele) {
                $(ele).find('.new_images_order').val((index + 1));
                newImagesOrderArray.push($(ele).find('img').attr('data-new_images_src'));
            });
            storeNewImages(newImagesOrderArray, 'REPLACE');
        }

        function newImagesOrderAble() {
            var newImagesArray;
            var newImagesOrderArray;
            $('.news_images_order_able').sortable({
                stop: function (event, ui) {
                    uniqueId = ui.item.attr('data-unique_id');
                    newImagesArray = $(this).find('.new_images_order_item:not(.waiting_to_del)');
                    newImagesOrderArray = [];

                    $(newImagesArray).each(function (index, value) {
                        console.log(index);
                        $(newImagesArray[index]).find('.new_images_order').val((index + 1));
                        newImagesOrderArray.push($(newImagesArray[index]).find('img').attr('data-new_images_src'));
                    });
                    console.log('newImagesOrderAble', ui.item, uniqueId, newImagesOrderArray);
                    storeNewImages(newImagesOrderArray, 'REPLACE');
                }
            });
        }
    </script>
@endsection
<div class='row' id='new_images_container_{{$uniqueId}}'>
    <textarea class="form-control" id="textarea_{{$uniqueId}}" name="demo_{{$variable_name}}"
              rows="10">{{$hidden or ''}}</textarea>

    <!-- Selected file value -->
    <input type='text' class="form-control" name="{{$variable_name}}" id='store_new_image_{{$uniqueId}}'
           value="{{$hidden or ''}}">

    <!-- Modal button -->
    <div class="col-md-12">
        <button type="button" class="btn btn-block btn-outline btn-primary bt_open_news_images_modal"
                id="bt_select_new_images_{{$uniqueId}}"
                data-toggle="modal"
                data-target="#new_images_modal_{{$uniqueId}}"
                data-unique_id="{{$uniqueId}}"
                data-cms_lang="{{$language->name}}"
                data-variable_name="{{$variable_name}}">
            <i class="fa fa-archive"></i> Select Images &nbsp;&nbsp;
            <span>
              @if($height != '*' && $width != '*')
                    (Appropriate size:&nbsp;{{$width}} x {{$height}} px)
                @elseif($width == '*' && $height != '*')
                    (Appropriate size:&nbsp;auto x {{$height}} px)
                @elseif($width != '*' && $height == '*')
                    (Appropriate size:&nbsp;{{$width}} x auto px)
                @else
                   {{'(Appropriate size: any size)'}}
                @endif
            </span>
        </button>
    </div>

    <!-- Display image -->
    <div class='col-md-12 p-t-20 news_images_order_able' id="display_new_images_section_{{$uniqueId}}">
        @if(isset($obj) && $obj)
            @for($i=0;$i<sizeof($values);$i++)
                <div class="col-md-12 cursor_sortable new_images_order_item"
                     id="new_images_display_container_{{$uniqueId.'_'.$i}}"
                     data-unique_id="{{$uniqueId}}"
                     data-cms_lang="{{$language->name}}"
                     data-variable_name="{{$variable_name}}">
                    <div class="col-md-2">
                        <img src="{{asset($values[$i]->image)}}" class="img-responsive"
                             data-new_images_src="{{$values[$i]->image}}">
                        <button type="button" class="btn btn-block btn-danger btn-xs "
                                data-new_images_src="{{$values[$i]->image}}"
                                data-unique_id="{{$uniqueId}}"
                                data-index="{{$i}}"
                                onclick="deleteNewImages(this)">Delete
                        </button>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <label class="control-label bold">Title</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_title[]"
                                   value="{{$values[$i]->title}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Alt</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_alt[]"
                                   value="{{$values[$i]->alt}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Caption</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_caption[]"
                                   value="{{$values[$i]->caption}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Order</label>
                            <input type="text" class="form-control new_images_order" name="{{$variable_name}}_order[]"
                                   value="{{$values[$i]->order}}">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="hr-line-dashed"></div>
                </div>
            @endfor
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="hr-line-dashed"></div>
</div>

<!-- New Images Modal -->
<div class="modal inmodal fade" id="new_images_modal_{{$uniqueId}}" tabindex="-1"
     role="dialog" aria-hidden="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg modal_new_image_manager">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close new_images_close_modal" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Select Images
                    @if($height != '*' && $width != '*')
                        (Appropriate size:&nbsp;{{$width}} x {{$height}} px)
                    @elseif($width == '*' && $height != '*')
                        (Appropriate size:&nbsp;auto x {{$height}} px)
                    @elseif($width != '*' && $height == '*')
                        (Appropriate size:&nbsp;{{$width}} x auto px)
                    @else
                        {{'(Appropriate size: any size)'}}
                    @endif
                </h4>
            </div>
            <div class="modal-body" id="modal_body_{{$uniqueId}}"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white new_images_close_modal" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary bt_save_news_images"
                        data-unique_id="{{$uniqueId}}"
                        data-property_name="{{$variable_name}}"
                        data-language_name="{{$language->name}}"
                        id="bt_save_news_images_{{$uniqueId}}">Save Selected
                </button>
            </div>
        </div>
    </div>
</div>

