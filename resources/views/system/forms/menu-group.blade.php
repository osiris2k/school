@extends('system.master')

@section('content')
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>All Menu Groups</h5>&nbsp;&nbsp;
                @if(!isset($obj))
                    <a href="{{url('system/menu-groups/create/')}}"><span class='badge badge-primary'>Create</span></a>

                @endif
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                {!! \Session::get('response') !!}
                @foreach($errors->all() as $error)
                    <p class="alert alert-danger">{{$error}}</p>
                @endforeach
                {!! Form::open(array('url'=>$url,'method'=>$method,'class'=>'form-horizontal','files'=>true))!!}
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Site</label>
                        <select name="site_id" class="form-control" required>
                            <option value="">Choose Site</option>
                            @foreach($sites as $site)
                                @if((isset($obj) ? $obj->site_id : '') == $site->id)
                                    <option value="{{$site->id}}" selected>{{$site->name}}</option>
                                @else
                                    <option value="{{$site->id}}">{{$site->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- @if(\App\Helpers\HotelHelper::isEnable())
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="control-label">Hotel</label>
                            <select name="hotel_id" id="hotel_id" class="form-control" required>
                                <option value="">Choose Hotel</option>
                                @foreach($hotels as $hotelIndex => $hotel)
                                    @if((isset($obj) && ($obj->hotel()->first() && $obj->hotel()->first()->pivot->hotel_id == $hotel->id)))
                                    <option value="{{$hotel->id}}" selected>{{$hotel->name}}</option>
                                    @else
                                        <option value="{{$hotel->id}}">{{ $hotel->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif -->

                <div class="form-group">
                    <div class="col-md-6">
                        <label class="control-label">Group Menu Name</label>
                        <input type="text" name="name" class="txt-lable form-control" required
                               @if(isset($obj))
                               value="{{$obj->name}}"
                                @endif
                        >
                    </div>
                </div>
                <div class="form-group action-zone">
                    <div class="col-sm-6 text-right">
                        <a href="{{url('system/menu-groups')}}" class="btn btn-white">Cancel</a>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop