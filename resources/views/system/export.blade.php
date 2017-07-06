<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<style>
		th{
			background-color: #f9f9f9;
			font-size: 10px !important;
		}
		tr > th {
		    border-bottom: 1px solid #000000;
		}
		tr > td {
		    border-bottom: 1px solid #000000;
		}
	</style>	
</head>
<body>
	<table>
		<thead>
			<tr>
				<th align="center" height="30" valign="middle">No.</th>
				@foreach($form_properties as $property)	
				<th align="center" height="30" valign="middle">{{$property->name}}</th>
				@endforeach	
			</tr>
		</thead>
		<tbody>
			<?php $i = 1 ?>
			@foreach($contents as $content)
			<tr>
				<td height="20" valign="middle">{{$i++}}</td>
				@foreach ($content->FormProperties as $property)
				<td  height="20" valign="middle">
					@if($property->data_type_id == 19)
						{{url($property->pivot->value)}}
						@else
						{{$property->pivot->value}}
					@endif
				</td>
				@endforeach
			</tr>
			@endforeach	
		</tbody>
	</table>
</body>
</html>