<?php
if(isset($obj)){
	$value       = CmsHelper::getValueByPropertyID($obj->id,$id,$page,$options_form);
}
?>
<div class="form-group">
	<div class="row">
		<div class="col-md-12">
			<label class="control-label">{{$lable or $name}}</label>
			<div class="checkbox">
				<?php
				$json = json_decode($options);
				?>
				<select {{ $is_mandatory==1 ? "required" : '' }} class="form-control m-b chosen-select-width" name="{{$variable_name}}">
					<option value="">Choose {{$name}}</option>
					@foreach($json[0]->value as $list)
						<option value="{{$list}}"
								@if(isset($obj)){
						@if($value==$list)
							{{'selected'}}
								@endif
								@endif
						>{{$list}}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
</div>