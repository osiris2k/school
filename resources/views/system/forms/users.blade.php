@extends('system.master')

@section('css')
<link href="{{ asset('system/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
<link href="{{ asset('system/css/plugins/summernote/summernote.css')}}" rel="stylesheet">
<link href="{{ asset('system/css/plugins/summernote/summernote-bs3.css')}}" rel="stylesheet">
<link href="{{ asset('system/vendors/image-picker/image-picker.css')}}" rel="stylesheet">
<link href="{{ asset('system/css/plugins/dropzone/basic.css')}}" rel="stylesheet">
<link href="{{ asset('system/css/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
<link href="{{ asset('system/css/plugins/cropper/cropper.min.css')}}" rel="stylesheet">
<link href="{{ asset('system/css/plugins/chosen/chosen.css')}}" rel="stylesheet">
<link href="{{ asset('system/css/plugins/datetimepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet">
@stop

@section('content')
<div class="col-lg-12">
	<div class="ibox float-e-margins">		
		<div class="ibox-content">		
			@foreach($errors->all() as $error)
				<p class="alert alert-danger">{{$error}}</p>
			@endforeach		
			{!! Form::open(array('url'=>$action,'method'=>$method,'class'=>'form-horizontal','files'=>true))!!}
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Email</label>						
						<input type="email" name="email" class="txt-lable form-control" value="{{ $obj->email or old('email') }}" @if(isset($obj)) readonly @endif>
					</div>
				</div>	
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Password</label>
						<input type="password" name="password" class="txt-lable form-control">
					</div>
				</div>	
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Confirm password</label>
						<input type="password" name="password_confirmation" class="txt-lable form-control">
					</div>
				</div>	
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Firstname</label>
						<input type="text" name="firstname" class="txt-lable form-control" value="{{ $obj->firstname or old('firstname') }}">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Lastname</label>
						<input type="text" name="lastname" class="txt-lable form-control" value="{{ $obj->lastname or old('lastname') }}">
					</div>
				</div>	

				<div class="form-group">
					<div class="col-md-6">
						<label class="control-label">Role</label>
						<select class="form-control m-b role" name="role_id" @if($user->role_id == 2 || $user->role_id == 3) disabled @endif>
							@foreach($roles as $role)
								<?php
									$selected = '';
									if(isset($obj))
									{
										if($obj->role_id == $role->id)
										{
											$selected = 'selected';
										}
									}
								?>
								<option value="{{$role->id}}" {{$selected}}>{{$role->name}}</option>
								
							@endforeach
				        </select>
				        @if(($user->role_id == 2 || $user->role_id == 3) && isset($obj)) 
							<input type='hidden' name='role_id' value="{{$obj->role_id}}">
						@elseif(($user->role_id == 2 || $user->role_id == 3) && $method == 'post')
							<input type='hidden' name='role_id' value="3">
						@endif
					</div>
				</div>
				
				@if(!empty($sites) &&($user->role_id != 3) || $method == 'post')
					<div class="form-group sites" style="display:none;">
						<div class="col-md-6">
							<label class="control-label">Sites</label>
							@foreach($sites as $site)
								<div class="checkbox">
									<label>
								    	<input type="checkbox" @if(in_array($site->id,$user_sites)) checked @endif name="sites[]" value="{{$site->id}}" @if($user->role_id == 2 || $user->role_id == 3) disabled @endif>
								    	{{$site->name}}
								  	</label>
								  	@if(($user->role_id == 2 || $user->role_id == 3) && in_array($site->id,$user_sites))
								  		<input type="hidden" name="sites[]" value="{{$site->id}}"> 
								  	@endif
								</div>
							@endforeach
						</div>
					</div>
					
				@endif
				@if(!empty($sites)  || $method == 'post' )	
					<div class="form-group editorial_sites" style="display:none;">
						<div class="col-md-6">
							<label class="control-label">Sites</label>
							@foreach($sites as $site)
								<div class="radio">
									<label>
								    	<input type="radio" name="checksite" @if(in_array($site->id,$user_sites)) checked @endif value="{{$site->id}}" @if($user->role_id == 3 ) disabled @endif>
								    	{{$site->name}}
								  	</label>
								  	@if($user->role_id == 3 && in_array($site->id,$user_sites))
								  		<input type="hidden" name="checksite" value="{{$site->id}}"> 
								  	@endif
								</div>
							@endforeach
						</div>
					</div>	
				@endif							
				<div class="form-group action-zone">
					<div class="col-sm-6 text-right">
						<a href="{{url('system/users/')}}" class="btn btn-white">Cancel</a>
						<button class="btn btn-primary" type="submit">Save</button>
					</div>
				</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@stop
@section('script')
	<script>
		$(function(){
		
			var sites_id = <?= json_encode($user_sites); ?>;
			function check(site,type){
				$('.ibox-content .'+site+' input[type="'+type+'"]').each(function(i){

					var site_id = parseInt($(this).val());
	
					if(jQuery.inArray(site_id,sites_id) > -1){
						$(this).prop('checked',true);
					}	
				});
			}
			function uncheck(site,type){
				$('.ibox-content .'+site+' input[type="'+type+'"]').each(function(){
					$(this).attr('checked',false);
				});
			}
				
				$('.form-group .role').change(function(){
					// if  role is admin 
					
					if($(this).val() == 2){
						$('.ibox-content .sites').css('display','block');

						uncheck('editorial_sites','radio');
						check('sites','checkbox');
						
					}else{
						$('.ibox-content .sites').css('display','none');
						uncheck('editorial_sites','checkbox');

					}
					// if  role is editorial 
					if($(this).val() == 3){
						$('.ibox-content .editorial_sites').css('display','block');
						uncheck('sites','checkbox');
						check('editorial_sites','radio');
					
					}else{
						$('.ibox-content .editorial_sites').css('display','none');
						uncheck('editorial_sites', 'radio');	
					}
					
				});

				// Default
				
				var role = $('.form-group .role').val();
				if( role == 3){

					$('.ibox-content .editorial_sites').css('display','block');
					uncheck('sites','checkbox');
				
				}else if( role == 2){

					$('.ibox-content .sites').css('display','block');
					uncheck('editorial_sites','radio');
				}
		});
	</script>
@stop