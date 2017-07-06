<?php
	if(isset($obj)){
		$values = json_decode(CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form));
		$hidden = CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form);
		$hidden = json_decode($hidden, true);
		$tmp_hidden = array();
		if($hidden==null)
		{
			$hidden = [];
		}
		foreach ($hidden as $hidden_obj) {
			$tmp_hidden[] = $hidden_obj['image'];
		}
		$hidden = json_encode($tmp_hidden);
	}

	$options = json_decode($options);
	$height = "*";
	$width = "*";

	if(!empty($options)){
		$width = array_shift($options);
		$width = (!empty($width->value[0])) ? $width->value[0] : '*';

		$height = array_shift($options);
		$height = (!empty($height->value[0])) ? $height->value[0] : '*';
	}
?>
<div class='gallery' id='gallery-{{$id}}'>
	<label class='control-label'>{{$name}}</label>
	<input type='hidden' name="{{$variable_name}}" value="{{$hidden or ''}}" id='txt-gallery-{{$id}}' {{{ $is_mandatory==1 ? 'required' : '' }}}>
	<div class='row'>
		<div class="col-xs-12 show-image">
			<div class="col-xs-12">
				<div class="row">
					<div class="col-xs-4 select-image">
						<!-- <i class='fa fa-plus'></i> -->
						@if($height != '*' && $width != '*')
							{{$width}} x {{$height}} px
						@elseif($width == '*' && $height != '*')
							auto x {{$height}} px
						@elseif($width != '*' && $height == '*')
							{{$width}} x auto px
						@else
							<i class='fa fa-plus'></i>
						@endif
					</div>
				</div>
			</div>
			<div id="order-image" class="order-image">
			@if(isset($obj))
				@for($i=0;$i<sizeof($values);$i++)
					<div class="old-image">
						<div class="row">
							<div class="col-xs-2">
								<img src="{{url($values[$i]->image)}}">
								<div class="badge-image">
									<span class="badge badge-danger" onclick="delete_image(this)">delete</span>
								</div>
							</div>
							<div class="col-xs-10">
								<div class="col-xs-10">
									<div class="col-xs-12">
										<label class="control-label bold">Title</label>
										<input type="text" class="form-control" name="{{$variable_name}}_title[]"
											   value="{{(isset($values[$i]->title)) ? $values[$i]->title : ''}}">
									</div>
									<div class="col-xs-12">
										<label class="control-label bold">Alt</label>
										<input type="text" class="form-control" name="{{$variable_name}}_alt[]"
											   value="{{(isset($values[$i]->alt)) ? $values[$i]->alt : ''}}">
									</div>
									<div class="col-xs-12">
										<label class="control-label bold">Caption</label>
										<input type="text" class="form-control" name="{{$variable_name}}_caption[]"
											   value="{{(isset($values[$i]->caption)) ? $values[$i]->caption : ''}}">
									</div>
									<input type="hidden" class="order form-control"
										   name="{{$variable_name}}_order[]" value="{{$i}}">
								</div>
							</div>
						</div>
					</div>
				@endfor
			@endIf
			</div>
		</div>
	</div>
</div>