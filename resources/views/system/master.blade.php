<!DOCTYPE html>
<html>
@include('system.include.header')
<body class="fixed-sidebar">
    <div id="wrapper">
        @include('system.include.menu')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    @include('system.include.nav')
                </nav>
            </div>
            @include('system.include.breadcrumb')

            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content  animated fadeInRight">
                        <div class="row">
                            @yield('content','')
                        </div>
                    </div>
                </div>
                @include('system.include.footer')
            </div>
        </div>
    </div>
    <div id="select-image">
        <div class="container">
            <div class="col-xs-12 select-image-wrapper">
                <select multiple="multiple" class="image-picker show-labels show-html masonry">                  
                </select>
                <div class="col-xs-12 action-btn">
                    <div class='pull-right'>
                        <button class='btn btn-primary' id='choose-image' data-target='#select-image' data-url=''>Choose</button>
                        <div class='btn btn-danger btn-close' data-target='#select-image'>Cancel</div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
    <div id="add-image">       
        <div class="container">
            <div class="col-xs-12 select-image-wrapper">
               {!! Form::open(array('url'=>url('system/uploadImagesAjax'),'method'=>'post','class'=>'dropzone','id'=>'my-awesome-dropzone','files'=>true))!!}
                    <div class="dropzone-previews"></div>                    
                    <div class="col-xs-12 action-btn">
                        <div class='pull-right'>
                            <button type='submit' data-target="#my-awesome-dropzone" class="btn btn-primary">Submit this form!</button>
                            <div class='btn btn-danger btn-close' data-target='#add-image'>Cancel</div>
                        </div>
                    </div>  
                </form>                     
            </div>
        </div>
    </div>
    <div id='spinner'>
         <div class="sk-spinner sk-spinner-circle">
            <div class="sk-circle1 sk-circle"></div>
            <div class="sk-circle2 sk-circle"></div>
            <div class="sk-circle3 sk-circle"></div>
            <div class="sk-circle4 sk-circle"></div>
            <div class="sk-circle5 sk-circle"></div>
            <div class="sk-circle6 sk-circle"></div>
            <div class="sk-circle7 sk-circle"></div>
            <div class="sk-circle8 sk-circle"></div>
            <div class="sk-circle9 sk-circle"></div>
            <div class="sk-circle10 sk-circle"></div>
            <div class="sk-circle11 sk-circle"></div>
            <div class="sk-circle12 sk-circle"></div>
        </div>
    </div>
    <div id='cropper'>
        <div class="container">
            <div class="col-xs-offset-1 col-xs-10 set-wrapper">
                {!! Form::open(array('url'=>url('system/uploadCropImage'),'method'=>'post','files'=>true))!!}
                <div class="col-xs-6">
                    <div class="image-crop">
                       <img src="">
                    </div>
                </div>
                <div class="col-xs-6">
                    <h4>Crop image</h4>
                    <div class="btn-group">
                        <button class="btn btn-primary" id="zoomIn" type="button">Zoom In</button>
                        <button class="btn btn-primary" id="zoomOut" type="button">Zoom Out</button>
                        <button class="btn btn-primary" id="rotateLeft" type="button">Rotate Left</button>
                        <button class="btn btn-primary" id="rotateRight" type="button">Rotate Right</button>
                        <button class="btn btn-info" id="download" type="button" onclick="saveCroppper($image.cropper('getDataURL'))">Save</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    @include('system.include.js')       
    @yield('script','')
    @yield('custom_script')
    @yield('custom_script_ext','')
</body>
</html>