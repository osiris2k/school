<strong>{{$submission_name}}</strong><br/>
<em>Submitted at: {{date('Y-m-d H:i:s')}}</em><br/>
<a href="{{$cms_url}}">View all submission</a>
<br/>
<br/>
<table>
	@foreach($data as $key => $value)
		<tr>
			<td>
				<strong>
					@if(array_key_exists($key,$properties_label))
						{{$properties_label[$key]['label']}}
					@else
						{{$key}}
					@endif
				</strong>
			</td>
			<td>
				@if(isset($uploaded_files))
					@if(array_key_exists($key,$uploaded_files))
						<a href="{{$uploaded_files[$key]['file_path']}}" target="_blank">Download Link</a>
					@else
						{{$value}}
					@endif
				@else
					{{$value}}
				@endif
			</td>
		</tr>
	@endforeach
</table>