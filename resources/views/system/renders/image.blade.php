<?php

// Set imageUniqueId for separate language tab
if (isset($content_object)) {
    $imageUniqueId = $variable_name . '_' . $language->name;
    $renderLanguage = $language->name;
} else {
    $imageUniqueId = $variable_name . '_' . $language;
    $renderLanguage = $language;
}

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
$imageAllowExt = '';
$height = "*";
$width = "*";

if (!empty($options)) {

    foreach ($options as $oItem) {
        if ($oItem->name == 'mimes') {
            //Set cms allow upload file
            $imageAllowExt = implode(',', $oItem->value);
        }
    }

    $width = array_shift($options);
    $width = (!empty($width->value[0])) ? $width->value[0] : '*';

    $height = array_shift($options);
    $height = (!empty($height->value[0])) ? $height->value[0] : '*';
}

?>
@section('custom_script')
    <script>
        // TODO check value missing after change folder (use Session)(not use now)
        // TODO clean code
        // TODO maybe add force to crop later
        // TODO add restore delete button and function
        var imageUniqueId = '{{$imageUniqueId}}';
        var imageCmsLanguage = '{{$renderLanguage}}';
        var imageVariableName = '{{$variable_name}}';
        var imageAssetPath = '{{asset('')}}';
        var imagePropertyName = '{{$variable_name}}';
        var imageAllowExt = '{{$imageAllowExt}}';
        var imageLanguageName = '';

        $(document).ready(function () {

            // Init New Image orderable
            imageSortable();

            $('.image_close_modal').click(function () {
                var imageIframeDocument = $('#image_iframe_' + imageUniqueId).contents();
                imageClearSelected(imageIframeDocument);
            });

            $(".content_tab_language").click(function () {
                imageSetConfig($(this).attr('data-language'));
            })

            $('.bt_image_open_modal').click(function () {
                imagePropertyName = imageVariableName = $(this).attr('data-variable_name');

                imageSetConfig($(this).attr('data-cms_lang'));
                imageGenerateIframe(imageUniqueId);
            });

            $('.bt_save_image').click(function () {
                imageUniqueId = $(this).attr('data-unique_id');
                imagePropertyName = $(this).attr('data-property_name');
                imageVariableName = $(this).attr('data-property_name');
                imageLanguageName = $(this).attr('data-language_name');

                var imageIframeModal = $('#image_modal_' + imageUniqueId);
                var imageIframeDocument = $('#image_iframe_' + imageUniqueId).contents();
                var imageSelectedVal = imageIframeDocument.find('#multiple_file_value').val();

                if (imageSelectedVal.length > 0) {
                    imageSelectedVal = JSON.parse(imageSelectedVal);
//                    imageSelectedVal = imageCheckNewData(imageSelectedVal);
                    imageStoreNewData(imageSelectedVal);
                    imageGenerateNewSelected(imageSelectedVal);
                    imageSortable();
                } else {
                    imageIframeModal.modal('hide');
                }

                // Clear selected file
                imageClearSelected(imageIframeDocument);
            });
        });

        function imageSetConfig(lang) {
            imageUniqueId = imageVariableName + '_' + lang;
            imageCmsLanguage = lang;
        }

        function imageGenerateIframe(imageUniqueId) {
            var iframeEle;
            var newImageModalBody = $('#image_modal_body_' + imageUniqueId);
            var newImageIframe = newImageModalBody.children('iframe');
            // TODO Set multiple_file to FALSE later
            if (newImageIframe.length == 0) {
                /*iframeEle = '<iframe id="image_iframe_' + imageUniqueId + '" class="new_image_iframe" width="100%" height="550"'
                        + 'frameborder="0"'
                        + 'src="{{ route('files_manager.system.get_file_manager_lib') }}?type=1&field_id=file_input&multiple_file=TRUE&cms_lang=' + imageCmsLanguage + '&variable_name=' + imageVariableName + '&override_allow_extension=' + imageAllowExt + '&subfolder={{config('upload.IMAGES_ROOT_DIR')}}">'
                        + '</iframe>';*/
                iframeEle = '<iframe id="image_iframe_' + imageUniqueId + '" class="new_image_iframe" width="100%" height="550"'
                        + 'frameborder="0"'
                        + 'src="{{ route('files_manager.system.get_file_manager_lib') }}?type=1&field_id=file_input&multiple_file=TRUE&cms_lang=' + imageCmsLanguage + '&variable_name=' + imageVariableName + '&override_allow_extension=' + imageAllowExt + '">'
                        + '</iframe>';
                $('#image_modal_body_' + imageUniqueId).html(iframeEle);
            }

            // Not use now
            //imageSyncSelectedFileInIframe();
        }

        /**
         * This method not use now its use for user can't select duplicate file
         */
        // TODO if have selected image should sync with value in iframe
        // TODO if delete image should remove selected file style in iframe
        function imageSyncSelectedFileInIframe() {
            var imageNewData = getStoreNewImage();
            var imageIframeDocument = $('#image_iframe_' + imageUniqueId).contents();
            imageIframeDocument.find('#multiple_file_value').val(imageNewData);
        }

        function imageClearSelected(iframe) {
            iframe.find('img').attr('style', 'border:0;');
            iframe.find('#multiple_file_value').val('');
        }

        /**
         * Not use now
         */
        function imageCheckNewData(imageNewData) {
            var imageValueContainer = $('#image_store_value_container_' + imageUniqueId);
            var newValToStore;

            if (imageValueContainer.val().length > 0) {
                newValToStore = JSON.parse(imageValueContainer.val());
                imageNewData = newValToStore.concat(imageNewData);
            }

            return imageNewData;
        }

        function imageStoreNewData(imageNewData, storeType) {
            var imageValueContainer = $('#image_store_value_container_' + imageUniqueId);
            console.log('imageStoreNewData', imageUniqueId, imageValueContainer.val(), typeof imageValueContainer.val());
            if (imageValueContainer.val().length > 0) {
                var newValToStore = JSON.parse(imageValueContainer.val());
                if (storeType === undefined) {
                    imageNewData = newValToStore.concat(imageNewData);
                }
            }

            imageValueContainer.val(JSON.stringify(imageNewData));
//            $('#textarea_' + imageUniqueId).val(JSON.stringify(imageNewData));
        }

        /**
         * Not use now
         */
        function getStoreNewImage() {
            var imageValueContainer = $('#image_store_value_container_' + imageUniqueId);
            return $('#textarea_' + imageUniqueId).val();
            // TODO after test use below return value
            return imageValueContainer.val();
        }

        function imageGenerateNewSelected(imageNewData) {
            var newImageDisplayContainer = $('#image_display_selected_section_' + imageUniqueId);
            var imageIframeModal = $('#image_modal_' + imageUniqueId);
            var appendData = '';
            var childIndex = imageFindItemIndex();
            $.each(imageNewData, function (index, val) {
                appendData += imageHtmlFormat(childIndex, val);
                childIndex++;
            });

            newImageDisplayContainer.append(appendData);
            imageIframeModal.modal('hide');
        }

        function imageFindItemIndex() {
            var newImageDisplayContainer = $('#image_display_selected_section_' + imageUniqueId);
            console.log(newImageDisplayContainer, newImageDisplayContainer.children('.image_order_item:not(.waiting_to_del)').length, newImageDisplayContainer.find('.image_order_item:not(.waiting_to_del)').length)
            return newImageDisplayContainer.children('.image_order_item:not(.waiting_to_del)').length;
        }

        function imageHtmlFormat(index, fileData) {
            var appendData = '<div class="col-md-12 cursor_sortable image_order_item" id="image_display_selected_container_' + imageUniqueId + '_' + index + '" data-unique_id="' + imageUniqueId + '" data-cms_lang="' + imageLanguageName + '" data-variable_name="' + imageVariableName + '">'; // Begin container
            appendData += '<div class="col-md-2">'; // Begin Image thumb container
            appendData += '<a href="' + imageAssetPath + fileData + '" class="fancybox"><img src="' + imageAssetPath + fileData + '" class="img-responsive" data-new_image_src="' + fileData + '"></a>';
            appendData += '<button type="button" class="btn btn-block btn-danger btn-xs " data-new_image_src="' + fileData + '" data-index="' + index + '" data-unique_id="' + imageUniqueId + '" data-cms_lang="' + imageLanguageName + '" data-variable_name="' + imageVariableName + '" onclick="imageDeleteData(this)">Delete</button>';
            appendData += '</div>'; // End Image thumb container
            appendData += '<div class="col-md-10">'; // Begin Image properties
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Caption Title</label><input type="text" class="form-control" name="' + imagePropertyName + '_title[]" value="">';
            appendData += '</div>';
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Alt</label><input type="text" class="form-control" name="' + imagePropertyName + '_alt[]" value="">';
            appendData += '</div>';
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Caption Text</label><input type="text" class="form-control" name="' + imagePropertyName + '_caption[]" value="">';
            appendData += '</div>';
            /**
             * Add Class
             * Edited by Piyaphong
             * Date 2016/04/05
             * @type {string}
             */
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Class</label><input type="text" class="form-control" name="' + imagePropertyName + '_class[]" value="">';
            appendData += '</div>';
            /** Add Link url
             * Edited by Boripat Boonlee
             * Date 2017/03/15
             * @type {string}
             ***/
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Link URL</label><input type="text" class="form-control" name="' + imagePropertyName + '_link_url[]" value="">';
            appendData += '</div>';
            appendData += '<div class="col-md-12">';
            appendData += '<label class="control-label bold">Video URL</label><input type="text" class="form-control" name="' + imagePropertyName + '_video_url[]" value="">';
            appendData += '</div>';
            appendData += '<div class="col-md-3">';
            appendData += '<label class="control-label bold">Position</label> <br><select name=' + imagePropertyName + '_position[]" class="form-control"><option value="center"> Center </option><option value="left">Left   </option><option value="right">Right </option></select>';
            appendData += '</div>';

            appendData += '<div class="col-md-12 hidden">';
            appendData += '<label class="control-label bold">Order</label><input type="text" class="form-control image_order" name="' + imagePropertyName + '_order[]" value="' + (index + 1) + '">';
            appendData += '</div>';
            appendData += '</div>'; // End Image properties
            appendData += '<div class="clearfix"></div>';
            appendData += '<div class="hr-line-dashed"></div>';
            appendData += '</div>'; // End container

            return appendData;
        }

        function imageDeleteData(deleteEle) {
            imageUniqueId = $(deleteEle).attr('data-unique_id');
            var removeVal = $(deleteEle).attr('data-new_image_src');
            var removeContainerIndex = $(deleteEle).attr('data-index');
            var removeIndexData = 0;
            var imageValueContainer = $('#image_store_value_container_' + imageUniqueId);
            console.log('imageDeleteData', typeof imageValueContainer.val(), imageValueContainer.val());
            var imageNewValueToStore = JSON.parse(imageValueContainer.val());
            var deleteParentContainer = $(deleteEle).closest('.new_image_order_able');

            removeIndexData = imageNewValueToStore.indexOf(removeVal);
            imageNewValueToStore.splice(removeIndexData, 1);
//            $('#image_display_selected_container_' + imageUniqueId + '_' + removeContainerIndex).remove();
            $('#image_display_selected_container_' + imageUniqueId + '_' + removeContainerIndex).addClass('waiting_to_del');
            $('#image_display_selected_container_' + imageUniqueId + '_' + removeContainerIndex).attr('id', 'image_display_selected_container_' + imageUniqueId + '_' + removeContainerIndex + '_waiting_to_del');
            imageReorderData(deleteParentContainer);
        }

        function imageReorderData(deleteParent) {
            var newImageOrderArray = [];

            $.each(deleteParent.children('.image_order_item:not(.waiting_to_del)'), function (index, ele) {
                $(ele).find('.image_order').val((index + 1));
                newImageOrderArray.push($(ele).find('img').attr('data-new_image_src'));
            });
            imageStoreNewData(newImageOrderArray, 'REPLACE');
        }

        function imageSortable() {
            var newImageArray;
            var newImageOrderArray;
            $('.new_image_order_able').sortable({
                stop: function (event, ui) {
                    imageUniqueId = ui.item.attr('data-unique_id');
                    newImageArray = $(this).find('.image_order_item:not(.waiting_to_del)');
                    newImageOrderArray = [];

                    $(newImageArray).each(function (index, value) {
                        console.log(index);
                        $(newImageArray[index]).find('.image_order').val((index + 1));
                        newImageOrderArray.push($(newImageArray[index]).find('img').attr('data-new_image_src'));
                    });
                    console.log('imageSortable', ui.item, imageUniqueId, newImageOrderArray);
                    imageStoreNewData(newImageOrderArray, 'REPLACE');
                }
            });
        }
    </script>
@append
<div class='row' id='image_main_container_{{$imageUniqueId}}'>
    <!--<textarea class="form-control" id="textarea_{{$imageUniqueId}}" name="demo_{{$variable_name}}"
              rows="10">{{$hidden or ''}}</textarea>-->

    <!-- Selected file value -->
    <input type='text' class="form-control hidden" name="{{$variable_name}}"
           id='image_store_value_container_{{$imageUniqueId}}'
           value="{{$hidden or ''}}">

    <!-- Modal button -->
    <div class="">
        <label class="control-label">{{$lable or $name}}</label>
        <button type="button" class="btn btn-block btn-outline btn-primary bt_image_open_modal"
                id="bt_selected_image_{{$imageUniqueId}}"
                data-toggle="modal"
                data-target="#image_modal_{{$imageUniqueId}}"
                data-unique_id="{{$imageUniqueId}}"
                data-cms_lang="{{$renderLanguage}}"
                data-variable_name="{{$variable_name}}"
                style="text-align: left;">
            <i class="fa fa-archive"></i> Select picture
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
    <div class='col-md-12 p-t-20 new_image_order_able' id="image_display_selected_section_{{$imageUniqueId}}">
        @if(isset($obj) && $obj)
            @for($i=0;$i<sizeof($values);$i++)
                <div class="col-md-12 cursor_sortable image_order_item"
                     id="image_display_selected_container_{{$imageUniqueId.'_'.$i}}"
                     data-unique_id="{{$imageUniqueId}}"
                     data-cms_lang="{{$renderLanguage}}"
                     data-variable_name="{{$variable_name}}">
                    <div class="col-md-2">
                        <a href="{{asset($values[$i]->image)}}" class="fancybox"><img
                                    src="{{asset($values[$i]->image)}}" class="img-responsive"
                                    data-new_image_src="{{$values[$i]->image}}"></a>
                        <button type="button" class="btn btn-block btn-danger btn-xs "
                                data-new_image_src="{{$values[$i]->image}}"
                                data-unique_id="{{$imageUniqueId}}"
                                data-index="{{$i}}"
                                onclick="imageDeleteData(this)">Delete
                        </button>
                    </div>
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <label class="control-label bold">Caption Title</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_title[]"
                                   value="{{(isset($values[$i]->title)) ? $values[$i]->title : ''}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Alt</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_alt[]"
                                   value="{{(isset($values[$i]->alt)) ? $values[$i]->alt : ''}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Caption Text</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_caption[]"
                                   value="{{$values[$i]->caption}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Class</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_class[]"
                                   value="{{(isset($values[$i]->class))? $values[$i]->class :''}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Link URL</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_link_url[]"
                                   value="{{(isset($values[$i]->link_url))? $values[$i]->link_url :''}}">
                        </div>
                        <div class="col-md-12">
                            <label class="control-label bold">Video URL</label>
                            <input type="text" class="form-control" name="{{$variable_name}}_video_url[]"
                                   value="{{(isset($values[$i]->video_url))? $values[$i]->video_url :''}}">
                        </div>
                        <div class="col-md-3">
                            <label class="control-label bold">Position</label><br>
                            <select name="{{$variable_name}}_position[]" id="" class="form-control">
                                <option value="center" {{(isset($values[$i]->position)) ? $values[$i]->position == 'center' ? 'selected' : '' :''}}>Center</option>
                                <option value="left" {{(isset($values[$i]->position)) ? $values[$i]->position == 'left' ? 'selected' : '' :''}}>Left</option>
                                <option value="right" {{(isset($values[$i]->position)) ? $values[$i]->position == 'right' ? 'selected' : '' :''}}>Right</option>
                            </select>
                        </div>
                        <div class="col-md-12 hidden">
                            <label class="control-label bold">Order</label>
                            <input type="text" class="form-control image_order" name="{{$variable_name}}_order[]"
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

<!-- New Image Modal -->
<div class="modal inmodal fade" id="image_modal_{{$imageUniqueId}}" tabindex="-1"
     role="dialog" aria-hidden="true"
     data-backdrop="static">
    <div class="modal-dialog modal-lg modal_new_image_manager">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close image_close_modal" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{{$name}}
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
            <div class="modal-body" id="image_modal_body_{{$imageUniqueId}}"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white image_close_modal" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary bt_save_image"
                        data-unique_id="{{$imageUniqueId}}"
                        data-property_name="{{$variable_name}}"
                        data-language_name="{{$renderLanguage}}"
                        id="bt_save_image_{{$imageUniqueId}}">Save Selected
                </button>
            </div>
        </div>
    </div>
</div>
