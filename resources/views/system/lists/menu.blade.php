@extends('system.master')

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Menu: {{$menu_group->name}}</h5>
            </div>
        </div>
        <div class="hr-line-dashed"></div>

        <div id="box-menu">
            @foreach($menus as $menu)
                <div class="ibox" data-id='{{$menu->id}}'>
                    <div class="ibox-content">
                        <?php
                        $hotelName = '';
                        if (\App\Helpers\HotelHelper::isEnable()) {
                            $content = \App\Content::find($menu->content_id);
                            $hotelName = (count($content->hotel) > 0) ? '(' . $content->hotel[0]->name . ')' : '';
                        }
                        ?>
                        <a href="{{url('system/menus/'.$menu->id.'/edit')}}">{{$menu->menu_title}} {{$hotelName}}</a>
                            <span style="float:right">
                                <a href="{{url('system/menus/'.$menu->id.'/edit')}}"><i class="fa fa-edit"></i></a>
                                &nbsp;&nbsp;|
                                {!! Form::open(array('url'=>'system/menus/'.$menu->id,'method'=>'delete','class'=>'inline-control form-ajax','onclick'=>'return(delete_type(this))'))!!}
                                <a class="delete-link">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                                {!! Form::close() !!}
                            </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop



@section('script')
    <script src="{{ asset('system/js/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            var element = "#box-menu";
            $(element).sortable().disableSelection();
            $(element).sortable({
                stop: function (event, ui) {
                    $('#spinner').css('top', $(window).scrollTop() + 'px');
                    $('html').css('overflow', 'hidden');
                    $('#spinner').show();
                    var ui_sortables = $('.ui-sortable .ibox');
                    var tmp_sort = [];
                    $(ui_sortables).each(function (index) {
                        tmp_sort[index] = $(ui_sortables[index]).data('id');
                    });
                    var type = 'menu';
                    $.get("{{URL::to('/')}}/system/order/" + type, {'sort[]': tmp_sort}, function (data) {
                        $('html').css('overflow', 'auto');
                        $('#spinner').hide();
                    });
                }
            });
        });
    </script>
@stop