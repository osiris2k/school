<?php
$palette = [];

$json = json_decode($options);
if (!empty($json)) {
    $tmp = array_shift($json);
    $palette = $tmp->value;
}
$color_selected = "#ffffff";
if (isset($obj)) {
    $color_selected = CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form);
}
$color = [];
foreach ($palette as $value) {
    $color[] = '"' . $value . '":"' . $value . '"';
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group color-picker-container">
            <label class="control-label">{{$lable or $name}}</label>
            <div data-format="alias" class="input-group color-picker col-md-2">
                <input type="text" name="{{$variable_name}}" class="form-control color-picker" title="{{$label_tooltip or ''}}"
                       placeholder="{{$label or $name}}" {{ $is_mandatory==1 ? "required" : '' }}
                       @if(isset($obj)) value="{{CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form)}}"
                       @else value="{{$color_selected}}" @endif
                />
                <span class="input-group-addon"><i></i></span>
            </div>
        </div>
    </div>
    <hr>
</div>
@section('css')
@endsection
@section('custom_script_ext')
    <script src="{{ asset('system/js/bootstrap-colorpicker.min.js')}}"></script>
    <script>
        $(function () {
            $(".color-picker-container").each(function () {
                console.log('Colorpicker already');
                @if(!empty($color))
               $(this).find(".color-picker").colorpicker({
                    colorSelectors: { {!!  implode(',',$color) !!} },
                    customClass: 'colorpicker-2x',
                    sliders: {
                        saturation: {
                            maxLeft: 200,
                            maxTop: 200
                        },
                        hue: {
                            maxTop: 200
                        },
                        alpha: {
                            maxTop: 200
                        }
                    }
                });
                @else
                $(this).find(".color-picker").colorpicker({
                    customClass: 'colorpicker-2x',
                    sliders: {
                        saturation: {
                            maxLeft: 200,
                            maxTop: 200
                        },
                        hue: {
                            maxTop: 200
                        },
                        alpha: {
                            maxTop: 200
                        }
                    }
                });

                @endif
            });
        });
    </script>
@endsection