<?php
if(isset($obj)){
	$value = CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form);
}
?>
<div class="form-group">
	<label class="control-label">{{$lable or $name}}</label>
	<div class="radio">
		<?php
		$json = json_decode($options);
		$values =  $json[0]->value;
		?>
		@foreach($values as $index => $list)
			<div class="radio">
				<label>
					<input type="radio" value="{{$list}}"
						   @if(isset($obj))
						   @if($value==$list)
						   {{'checked'}}
						   @endif
						   @elseif($index == 0)
						   {{'checked'}}
						   @endif
						   name="{{$variable_name}}">{{$list}}
				</label>
			</div>
		@endforeach
	</div>
</div>