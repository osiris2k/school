<?php

if (isset($content_object)) {
    $fileUniqueId = $variable_name . '_' . $language->name;
    $renderLanguage = $language->name;
} else {
    $fileUniqueId = $variable_name . '_' . $language;
    $renderLanguage = $language;
}

$type = array('png' => 'fa fa-file-image-o', 'jpg' => 'fa fa-file-image-o', 'gif' => 'fa fa-file-image-o', 'zip' => 'fa fa-file-zip-o');
if (isset($obj)) {
    $value = CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form);
//    if ($value != "") {
//        $value = explode("/", $value);
//        $file_name = end($value);
//        $extension = explode('.', $file_name);
//    }
} else {
    $value = '';
}
$options = json_decode($options);
// Set default allow ext
$fileAllowExt = 'jpg,jpeg,png,gif,pdf,zip,7z,rar';
$fileAllowSize = 0;

if (!empty($options)) {

    foreach ($options as $oItem) {
        if ($oItem->name == 'mimes') {
            //Set allow ext
            $fileAllowExt = $oItem->value[0];
        }
        if ($oItem->name == 'max') {
            // Set allow size
            $fileAllowSize = $oItem->value[0];
        }
    }
}

?>
@section('custom_script')
    <script>
        var fileUniqueId = '';
        var fileCmsLanguage = '{{$renderLanguage}}';
        var fileVariableName = '{{$variable_name}}';

        function deleteFileSelect(fileUniqueId) {
            fileUniqueId = $('#bt_file_clear_selected_' + fileUniqueId).attr('data-unique_id');
            fileVariableName = $('#bt_file_clear_selected_' + fileUniqueId).attr('data-property_name');
            fileCmsLanguage = $('#bt_file_clear_selected_' + fileUniqueId).attr('data-language_name');

            var fileValueContainer = $('#file_store_value_container_' + fileUniqueId);
            fileValueContainer.val('');
        }

        function saveFile(fileUniqueId) {
                fileUniqueId = $('#bt_file_save__'+fileUniqueId).attr('data-unique_id');
                fileVariableName = $('#bt_file_save__'+fileUniqueId).attr('data-property_name');
                fileCmsLanguage = $('#bt_file_save__'+fileUniqueId).attr('data-language_name');

                var fileIframeModal = $('#file_modal_' + fileUniqueId);
                var fileIframeDocument = $('#file_iframe_' + fileUniqueId).contents();
                var fileSelectedVal = fileIframeDocument.find('#multiple_file_value').val();

                if (fileSelectedVal.length > 0) {
                    fileSelectedVal = JSON.parse(fileSelectedVal);
                    console.log(fileSelectedVal);
                    fileStoreNewData(fileSelectedVal[0],fileUniqueId);
                }

                // Clear selected file
                fileClearSelected(fileIframeDocument);
                fileIframeModal.modal('hide');
        }

        function generateFileinput(fileUniqueId) {
            fileSetConfig($('#bt_select_file_' + fileUniqueId).attr('data-cms_lang'));
            fileGenerateIframe(fileUniqueId);
        }

        function fileSetConfig(lang) {
            fileUniqueId = fileVariableName + '_' + lang;
            fileCmsLanguage = lang;
//            console.log(fileUniqueId, fileCmsLanguage);
        }

        function fileClearSelected(iframe) {
            iframe.find('img').attr('style', 'border:0;');
            iframe.find('#multiple_file_value').val('');
        }

        function fileStoreNewData(fileNewData,fileUniqueId) {
//            console.log(fileNewData);
            var fileValueContainer = $('#file_store_value_container_' + fileUniqueId);
            fileValueContainer.val(fileNewData);
        }

        function fileGenerateIframe(fileUniqueId) {
            var iframeEle;
            var fileModalBody = $('#file_modal_body_' + fileUniqueId);
            var fileIframe = fileModalBody.children('iframe');
            $('#file_modal_body_' + fileUniqueId + ' iframe').remove();
            iframeEle = '<iframe id="file_iframe_' + fileUniqueId + '" class="file_iframe" width="100%" height="550"'
                    + 'frameborder="0"'
                    + 'src="{{ route('files_manager.system.get_file_manager_lib') }}?type=0&field_id={{$fileUniqueId}}&subfolder={{config('upload.FILE_ROOT_DIR')}}&multiple_file=FALSE&fldf=">'
                    + '</iframe>';
            fileModalBody.html(iframeEle);
        }

    </script>
@append

<div class="row m-b-20">
    <label class="control-label">{{$label or $name}}</label>
    <div class="pl-uploader col-md-12" id="pl-uploader-{{$variable_name}}-{{$renderLanguage}}"
         data-variable_name="{{$variable_name}}"
         data-language_name="{{$renderLanguage}}"
         data-max_file_size="{{$fileAllowSize}}kb"
         data-allow_mime_type="{{$fileAllowExt}}" style="padding:0px">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
    </div>
    <div class="">
        <div class="input-group">
            <input type="text" class="form-control" name="{{$variable_name}}"
                   id="file_store_value_container_{{$fileUniqueId}}" value="{{(isset($value)) ? $value : ""}}"
                   readonly>
            <span class="input-group-btn">
                 <button type="button" class="btn btn-danger bt_file_delete_selected"
                         id="bt_file_clear_selected_{{$fileUniqueId}}"
                         data-unique_id="{{$fileUniqueId}}"
                         data-cms_lang="{{$renderLanguage}}"
                         data-variable_name="{{$variable_name}}" onclick="return deleteFileSelect('{{$fileUniqueId}}')"><i
                             class="fa fa-trash"></i> Clear
                 </button>
                <button type="button" class="btn btn-primary bt_file_open_modal"
                        id="bt_select_file_{{$fileUniqueId}}"
                        data-toggle="modal"
                        data-target="#file_modal_{{$fileUniqueId}}"
                        data-unique_id="{{$fileUniqueId}}"
                        data-cms_lang="{{$renderLanguage}}"
                        data-variable_name="{{$variable_name}}" onclick="return generateFileinput('{{$fileUniqueId}}')"><i
                            class="fa fa-archive"></i> Select {{$name}}
                </button>
            </span>
        </div>
        <!-- Select File Modal -->
        <div class="modal inmodal fade" id="file_modal_{{$fileUniqueId}}" tabindex="-1"
             role="dialog" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg modal_new_image_manager">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close file_close_modal" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">{{$name}} </h4>
                    </div>
                    <div class="modal-body" id="file_modal_body_{{$fileUniqueId}}"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white file_close_modal" data-dismiss="modal">Close
                        </button>
                        <button type="button" class="btn btn-primary bt_file_save"
                                data-unique_id="{{$fileUniqueId}}"
                                data-property_name="{{$variable_name}}"
                                data-language_name="{{$renderLanguage}}"
                                id="bt_file_save__{{$fileUniqueId}}" onclick="saveFile('{{$fileUniqueId}}')">Select File
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hr-line-dashed"></div>


