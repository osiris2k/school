<p>Dear User,</p>

<p>Your message has been successfully sent, all information received will always remain confidential. <br>
We will contact you as soon as we review your message.</p>

<br/>
<p> Submission details </p>
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

<p>Regards</p>