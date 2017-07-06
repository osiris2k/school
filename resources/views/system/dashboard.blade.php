@extends('system/master')


@section('css')
    <link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
@stop

@section('content')
    <div class="row" id="sortable-view">
        @if(isset($sites) && !empty($sites))
            <div class="col-lg-6 panel-sortable">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Site - <span class="small">{{count($sites)}} site(s)</span></h5>

                        <div class="ibox-tools">
                            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 4)
                                <a class="" href="/system/sites/create" target="_blank">
                                    <i class="fa fa-plus"></i>
                                </a>
                            @endif
                            <a target="_blank" href="/system/sites" title="All site">
                                <i class="fa fa-share"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="table panel-table ">
                            <thead>
                            <tr>
                                <th>Last Site</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sites as $siteIndex => $siteItem)
                                <tr>
                                    <td>{{$siteItem->name}}</td>
                                    <td class="text-center">
                                        <a target="_blank" href="/system/sites/{{$siteItem->id}}/edit" title="edit">
                                            <i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($content_obj_types) && !empty($content_obj_types))
            @foreach($content_obj_types as $objTypeIndex => $objTypeItem)
                <?php
                $contentObj = $objTypeItem->content_objects;
                $contents = \App\Content::getContentByContentObj($objTypeItem->content_objects->lists('id'))
                        ->orderData('updated_at', 'desc')->get();
                ?>
                <div class="col-lg-6 panel-sortable">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>{{$objTypeItem->content_object_types_name}}
                                - <span class="small">
                                    {{count($contents)}}
                                    {{$objTypeItem->content_object_types_name.'(s)'}}</span></h5>

                            <div class="ibox-tools">
                                <div class="pull-left">
                                    <a class="dropdown-toggle"
                                       data-toggle="dropdown" href="#" title="New content">
                                        <i class="fa fa-plus"></i>
                                    </a>

                                    <div class="dropdown-menu dashboard-dropdown">
                                        <div class="col-md-12 no-pad">
                                            <select class="chosen-select-width form-control ignore-change"
                                                    onchange="dashboardRedirect(this)">
                                                <option data-redirect_url="">New {{$objTypeItem->content_object_types_name}}...</option>
                                                @foreach($contentObj as $contentOjbIndex => $contentObjItem)
                                                    <option data-redirect_url="{{asset('/system/contents/create/'.$contentObjItem->id)}}">
                                                        {{strtoupper(str_replace('_',' ',$contentObjItem->name))}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="pull-left">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" title="See all content">
                                        <i class="fa fa-share"></i>
                                    </a>

                                    <div class="dropdown-menu dashboard-dropdown">
                                        <div class="col-md-12 no-pad">
                                            <select class="chosen-select-width form-control ignore-change"
                                                    onchange="dashboardRedirect(this)">
                                                <option data-redirect_url="">See all {{$objTypeItem->content_object_types_name}}...</option>
                                                @foreach($contentObj as $contentOjbIndex => $contentObjItem)
                                                    <option data-redirect_url="{{asset('/system/contents/'.$contentObjItem->id)}}">
                                                        {{strtoupper(str_replace('_',' ',$contentObjItem->name))}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <table class="table panel-table ">
                                <thead>
                                <tr>
                                    <th>Last {{$objTypeItem->content_object_types_name}}</th>
                                    <th>Type</th>
                                    <th>Site</th>
                                    <th width="15%" class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($contents->take(5) as $contentIndex => $contentItem)
                                    <tr>
                                        <td>
                                            <span title="{{$contentItem->name}}">{{str_limit($contentItem->name,20)}}</span>
                                        </td>
                                        <td>{{strtoupper(str_replace("_"," ",$contentItem->contentObject->name))}}</td>
                                        <td>{{$contentItem->site->name}}</td>
                                        <td class="text-center">
                                            <a target="_blank" href="/system/contents/{{$contentItem->id}}/edit"
                                               title="edit">
                                                <i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@stop

@section('script')
    <script src="{{asset('vendors/js-cookie-master/src/js.cookie.js')}}"></script>
    <script>
        //        panelSortAble();

        function panelSortAble() {
            var element = "#sortable-view";
            var handle = ".ibox-title";
            var connect = "[class*=panel-sortable]";
            var sortEle = $(element).sortable(
                    {
                        handle: handle,
                        connectWith: connect,
                        tolerance: 'pointer',
                        forcePlaceholderSize: true,
                        opacity: 0.8,
                        update: function (event, ui) {
                            console.log(ui);
                        }
                    })
                    .disableSelection();
        }

        function dashboardRedirect(ele) {
            var redirectUrl = $(ele).children('option:selected').attr('data-redirect_url');

            window.open(redirectUrl, '_blank');
        }

        $(document).ready(function () {
            $('.dashboard-dropdown').click(function (event) {
                event.stopPropagation();
            });
        });
    </script>
@stop