@extends('system.master')
@section('css')
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@endsection
@section('content')
    @if(count($sites) > 0)
        <div class="col-xs-12">
            <p>Filter by :</p>
        </div>
        {!! Form::open(array(
        'id' => 'contentReorderFilter',
        'route' => 'content_reorder',
        'method' => 'post',
        'class'=>'form-horizontal ')) !!}
        <div class="col-xs-3">
            <label class="control-label col-xs-4">Site and Hotel:</label>
            <div class="col-xs-8">
                <select id="site-filter" class="form-control m-b ignore-change chosen-select ">
                    @if(count($sites) > 0)
                        @foreach($sites as $site)
                            <option value="{{$site->id}}" data-type="site"
                                    @if($reorder_type === 'site' && $site->id==$selected_site_id) selected @endif>{{$site->name}}</option>
                        @endforeach
                        @if(config('hotel.ENABLE') === true && isset($hotels))
                            @foreach($hotels as $hotel)
                                <option value="{{$hotel->id}}" data-type="hotel"
                                        @if($reorder_type === 'hotel' && $hotel->id==$selected_site_id) selected @endif>{{$hotel->name}}</option>
                            @endforeach
                        @endif
                    @else
                        <option value="">No Data</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-xs-3">
            <label class="control-label col-xs-4">Content:</label>
            <div class="col-xs-8">
                <select id="content-filter" name="content-filter"
                        class="form-control m-b ignore-change chosen-select ">
                    <option value="0">Root</option>
                    @if(count($content_lists) > 0)
                        @foreach($content_lists as $content)
                            <option value="{{$content->id}}"
                                    @if($content->id==$selected_content_id) selected @endif>{{$content->name}}</option>
                        @endforeach
                    @else
                        <option value="">No Data</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-xs-3 text-left">
            <button type="button" id="reorderFormFilterBt" class="btn btn-outline btn-primary">Filter</button>
        </div>
        {!! Form::close() !!}
    @endif
    <div class="clearfix gap-20"></div>
    <div class="col-xs-12">
        <div class="ibox-content">
            @if(count($contents) == 0)
                <h5>No Data</h5>
            @else
                {!! Form::open(array('route' => 'update_content_order','method' => 'post', 'id' => 'contentReorderForm')) !!}
                <div class="dd">
                    <ol class="sortable dd-list">
                        {!! $content_reorder_html !!}
                    </ol>
                </div>
                {!! Form::close() !!}
                <div class="clearfix gap-20"></div>
                <button class="btn btn-primary ma-t-2-0" type="button" id="update-reorder">
                    <i class="fa fa-check"></i>&nbsp;Update
                </button>
            @endif
        </div>
    </div>
@stop

@section('script')
    <script src="{{asset('system/js/plugins/nestable/jquery.nestable.js')}}"></script>
    <script src="{{asset('vendors/jquery-blockui/jquery.blockUI.js')}}"></script>

    <script type="text/javascript">
        $('#reorderFormFilterBt').click(function () {
            var siteId = $('#site-filter').val();
            var contentId = $('#content-filter').val();
            var redirectUrl = '{{route('content_reorder')}}/' + $('#site-filter').find('option:selected').attr('data-type') + '/' + siteId + '/' + contentId;
            window.location.href = redirectUrl;
        });

        var updateOutput = function () {
            $.blockUI({
                message: '<h3><img src="{{asset('assets/images/circle-preloading.gif')}}"> Updating...</h3>',
                css: {
                    border: 'none',
                    padding: '10px',
                }
            });
            $.ajax({
                url: '{{route('update_content_order')}}',
                method: 'post',
                data: {
                    _token: '{{csrf_token()}}',
                    order_data: $('.dd').nestable('serialize')
                },
                dataType: 'json',
                success: function (data) {
                    $.unblockUI();
                }
            });
        };

        $(document).ready(function () {
            $('.dd').nestable({
                maxDepth: 1
            });

            $('#update-reorder').click(updateOutput);

            $("#site-filter").change(function (e) {
                var siteId = $('#site-filter').val();
                var redirectUrl = '{{route('content_reorder')}}/' + $('#site-filter').find('option:selected').attr('data-type') + '/' + siteId + '/0';

                window.location.href = redirectUrl;
            });

        });
    </script>
@endsection
