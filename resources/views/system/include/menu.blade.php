<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                    {{-- <img alt="image" class="img-circle" src="{{ asset('system/img/profile_small.jpg')}}" /> --}}
                </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                    <span class="clear"> <span class="block m-t-xs"> <strong
                                    class="font-bold">{{Auth::user()->firstname.' '.Auth::user()->lastname}}</strong>
                    </span> <span class="text-muted text-xs block">{{Auth::user()->role->name}}<b
                                    class="caret"></b></span> </span> </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        {{-- <li><a href="profile.html">Profile</a></li> --}}
                        {{-- <li><a href="contacts.html">Contacts</a></li> --}}
                        {{-- <li class="divider"></li> --}}
                        <li><a href="{{url('system/logout')}}">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    Quo Glo-bal
                </div>
            </li>
            <li data-tag-menu='dashboard' @if($tag_first_menu=='dashboard')class="active" @endif>
                <a href="{{route('dashboard.system.get_index')}}">
                    <i class="fa fa-desktop"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
            </li>

            @if(\App\Helpers\HotelHelper::isEnable())
                <li data-tag-menu='hotels' @if($tag_first_menu=='HOTEL')class="active" @endif>
                    <a href="#">
                        <i class="fa fa-th-large"></i>
                        <span class="nav-label">Hotels</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == 'HOTEL_LIST') ? 'active' : ''}}">
                            <a href="{{route('hotel.system.get_hotel_list')}}">Show Hotels</a>
                        </li>
                        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 4)
                            <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == 'CREATE_HOTEL') ? 'active' : ''}}">
                                <a href="{{route('hotel.system.get_create_hotel')}}">Create Hotel</a>
                            </li>
                            <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == 'HOTEL_PROPERTY') ? 'active' : ''}}">
                                <a href="{{route('hotel.system.get_set_hotel_property')}}">Set Hotel Properties</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <li data-tag-menu='menu-objects' @if($tag_first_menu=='menu-objects')class="active" @endif>
                <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Menus</span> <span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level capitalize">
                    <li><a href="{{ url('system/menu-groups') }}">Group Menu</a></li>
                    <li><a href="{{ url('system/menus') }}">Menu</a></li>
                </ul>
            </li>

            @if(isset($content_object_types_composer))
                @foreach($content_object_types_composer as $cot_item)
                    @if($cot_item->content_objects->count() > 0)
                        <li data-tag-menu='{{$cot_item->content_object_type}}'
                            @if($tag_first_menu==$cot_item->content_object_type)class="active" @endif>
                            <a href="#">
                                <i class="fa fa-th-large"></i>
                                <span class="nav-label">{{$cot_item->content_object_types_name}}</span>
                                <span class="fa arrow"></span>
                            </a>
                            <ul class="nav nav-second-level text-capitalize {{ strtolower($cot_item->content_object_type)}}">
                                @if($cot_item->id == 1)
                                    @if($cot_item->content_objects->count() > 0)
                                        <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == 'CONTENTS') ? 'active' : ''}}">
                                            <a href="{{url('system/contents/'.$cot_item->id)}}">
                                                <span class="nav-label">All Contents</span></a>
                                        </li>
                                        <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == 'CONTENT_RERODER') ? 'active' : ''}}">
                                            <a href="{{route('content_reorder')}}">
                                                <span class="nav-label">Contents Reorder</span></a>
                                        </li>
                                        <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == 'CONTENT_IMPORT_TRANSLATION') ? 'active' : ''}}">
                                            <a href="{{route('content.system.import.get_get_import_content_form_view')}}">
                                                <span class="nav-label">Import Translations</span></a>
                                        </li>
                                        <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == 'CONTENT_EXPORT_TRANSLATION') ? 'active' : ''}}">
                                            <a href="{{route('content.system.export.get_get_export_content_list_view')}}">
                                                <span class="nav-label">Export Translations</span></a>
                                        </li>
                                    @endif
                                @else
                                    @foreach($cot_item->content_objects as $menu_content)
                                        <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == $menu_content->name) ? 'active' : ''}}
                                            <?php if(strtolower($cot_item->content_object_type) == 'layout'){ echo $menu_content->name!='three_zone' && $menu_content->name !='video' ? 'preview' : ''; }?>"
                                            <?php if(strtolower($cot_item->content_object_type) == 'layout'){ echo 'data-img="/system/img/layouts/'.$menu_content->name.'.jpg" data-title="'.ucfirst(str_replace("_", "-", $menu_content->name)).'"';}?>
                                        >
                                            <a href="{{url('system/contents/'.$cot_item->id.'/'.$menu_content->id)}}">{{str_replace("_", "-", $menu_content->name)}}</a>

                                        </li>
                                    @endforeach
                                    @if($cot_item->content_objects->count() > 0)
                                        <li class="{{(isset($tag_sub_menu) && $tag_sub_menu == $cot_item->content_object_types_name) ? 'active' : ''}}">
                                            <a href="{{route('reorder.system.get_get_reorder_list',['site',$cot_item->id])}}">{{$cot_item->content_object_types_name."\tReorder"}}</a>
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        </li>
                    @endif
                @endforeach
            @endif

            @if(Auth::user()->role_id != 3 )
                <li data-tag-menu='submission'>
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Submission</span> <span
                                class="fa arrow"></span></a>
                    <ul class="nav nav-second-level text-capitalize">
                        @foreach(\App\Helpers\CmsHelper::getFormObjects() as $menu_form)
                            <li><a href="{{url('system/forms/'.$menu_form->id)}}">
                                    {{str_replace("_", " ", $menu_form->name)}}</a></li>
                        @endforeach
                    </ul>
                </li>
                @if(Auth::user()->role_id==1 )
                    <li data-tag-menu='form'>
                        <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Form</span> <span
                                    class="fa arrow"></span></a>
                        <ul class="nav nav-second-level text-capitalize">
                            @foreach(\App\Helpers\CmsHelper::getFormObjects() as $menu_form)
                                <li><a href="{{url('system/forms/create/'.$menu_form->id)}}">
                                        {{str_replace("_", " ", $menu_form->name)}}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif

                <li data-tag-menu='language'>
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Languages</span> <span
                                class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="{{ url('system/languages') }}">Show Languages</a></li>
                        <li><a href="{{ url('system/languages/create') }}">Create Languages</a></li>
                        <li><a href="{{ url('system/languages/reorder') }}">Reorder Languages</a></li>
                    </ul>
                </li>
            @endif
            @if(Auth::user()->role_id==1 )
                <li data-tag-menu='content-objects'>
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Content Setting</span> <span
                                class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="{{ url('system/content-objects') }}">Content Object</a></li>
                        <li><a href="{{ url('system/content-objects/create') }}">Create Content Object</a></li>
                    </ul>
                </li>
                <li data-tag-menu='form-objects'>
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Form Setting</span> <span
                                class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="{{ url('system/form-objects') }}">Form Object</a></li>
                        <li><a href="{{ url('system/form-objects/create') }}">Create Form Object</a></li>
                    </ul>
                </li>
            @endif
            {{--@if(Auth::user()->role_id==2||Auth::user()->role_id==1)  --}}
            <li data-tag-menu='user'>
                <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Users Management</span> <span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="{{ url('system/users') }}">Show users</a></li>
                    @if(Auth::user()->role_id != 3)
                        <li><a href="{{ url('system/users/create') }}">Create user</a></li>
                    @endif
                </ul>
            </li>
            <li data-tag-menu='translation'>
                <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Translation</span> <span
                            class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="{{ url('system/translations') }}">Show Translations</a>
                    </li>
                    @if(Auth::user()->role_id != 3)
                        <li>
                            <a href="{{ url('system/translations/create') }}">Create Translation</a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ url('system/translations/export') }}">Export Translations</a>
                    </li>
                    <li>
                        <a href="{{ url('system/translations/import') }}">Import Translations</a>
                    </li>
                </ul>
            </li>
            @if(Auth::user()->role_id !== 3)
                <li data-tag-menu='redirecturls'>
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Redirect URL</span> <span
                                class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="{{ url('system/redirecturls') }}">Show Redirect URL</a>
                        </li>
                        <li>
                            <a href="{{ url('system/redirecturls/create') }}">Create Redirect URL</a>
                        </li>
                    </ul>
                </li>

                <li data-tag-menu='site-objects'>
                    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">System Configuration</span> <span
                                class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li><a href="{{ url('system/sites') }}">Show Sites</a></li>
                        @if(Auth::user()->role_id == 1)
                            <li><a href="{{ url('system/sites/create') }}">Create Site</a></li>
                            <li><a href="{{ url('system/site-properties/create') }}">Set Site Properties</a></li>
                        @endif
                    </ul>

                </li>
            @endif
        </ul>
    </div>
</nav>