@extends('system.master')

@section('content')
<div class="col-lg-12">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>{{$page_header}}</h5>
		</div>
	</div>
	<div class="hr-line-dashed"></div>

	<div id="box-language">
		@foreach($language_items as $item)
		<div class="ibox" data-id='{{$item->id}}'>
			<div class="ibox-content">
				{{$item->name}}
				<span style="float:right">
					<i class="fa fa-bars"></i>
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
	$(document).ready(function(){
		var element = "#box-language";
	    $(element).sortable().disableSelection();
	    $(element).sortable({
	        stop: function(event,ui){
	        	$('#spinner').css('top',$(window).scrollTop()+'px');
                $('html').css('overflow','hidden');
                $('#spinner').show();
	        	var ui_sortables = $('.ui-sortable .ibox');
	        	var tmp_sort = [];
	        	$(ui_sortables).each(function(index){
	        		tmp_sort[index] = $(ui_sortables[index]).data('id');
	        	});
	        	var type = 'language';
	        	$.get( "{{URL::to('/')}}/system/order/"+type ,{'sort[]': tmp_sort},function(data){
	                $('html').css('overflow','auto');
	                $('#spinner').hide();
	        	});
	        }
	    });
	});
	</script>
@stop