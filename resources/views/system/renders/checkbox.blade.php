<?php
if (isset($obj)) {
	$value = CmsHelper::getValueByPropertyID($obj->id, $id, $page, $options_form);
	$value_array = json_decode($value, true);

	$tmp_array = array();
	for ($i = 0; $i < sizeof($value_array); $i++) {
		$tmp_array[] = $value_array[$i];
	}
}
?>
<div class="form-group">
	<label class="control-label">{{$lable or $name}}</label>
	<div class="checkbox">
		<?php
		$json = json_decode($options);
		//			$listByString = explode(',', $json[0]->value[0]);
		$listByString = $json[0]->value;
		$listByObject = $json[1]->value;
		?>
		@if(count($listByString) > 0 && $listByString[0] !== '')
			@foreach($listByString as $list)
				<div class="radio">
					<label>
						<input type="checkbox"
							   @if(isset($obj)){
							   @if(in_array($list,$tmp_array))
							   {{'checked'}}
							   @endif
							   @endif
							   value="{{$list}}"
							   {{ $is_mandatory==1 ? "required" : '' }} name="{{$variable_name}}[]">{{$list}}
					</label>
				</div>
			@endforeach
		@else
			<?php
			$objContents = CmsHelper::getPagesByContentObjectList(explode(',', $listByObject[0]));
			?>
			@foreach($objContents as $content)
				<div class="radio">
					<label>
						<input type="checkbox" value="{{$content->id}}"
							   @if(isset($obj)){
							   @if(in_array($content->id,$tmp_array))
							   {{'checked'}}
							   @endif
							   @endif
							   {{ $is_mandatory==1 ? "required" : '' }} name="{{$variable_name}}[]">{{$content->name}}
					</label>
				</div>
			@endforeach
		@endif
	</div>
</div>