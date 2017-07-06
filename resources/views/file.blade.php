{!! Form::open(array('url'=>'view/file','method'=>'post','files'=>true)) !!}
	<input name='photos' type="file">
	<input name='images' type="file">
	<input name='text' type="text">
	<input type="submit">
{!! Form::close(); !!}