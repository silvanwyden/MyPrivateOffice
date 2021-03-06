@extends('layouts.app')

@section('content')
	<div class="container">
	
		<div class="flash-message">
		    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
		      @if(Session::has('alert-' . $msg))
		
		      <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
		      @endif
		    @endforeach
	  	</div> <!-- end .flash-message -->
	
		<form action="/passpack" method="POST" class="form-horizontal" id="myform">
			{{ csrf_field() }}
	
			<div class="row" style="padding-bottom: 15px;">
				<div class="col-sm-8">
				  <div class="btn-group" role="group" aria-label="first">
				  
				  		<a href="/passpacks?page={{ $page }}" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
				  		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
				  		<button type="submit" class="btn btn-info" name="save_edit" value="save_edit" ><span class="glyphicon glyphicon-floppy-saved"></span> Save&Edit</button>
			  			
					</div>
				</div>
			</div>
		
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
					
						<div class="col-sm-10">
							PassPack
						</div>
					
						<div class="col-sm-2" style="text-align: right;">
					
							@if ($counter > 0)
								{{ $counter }}/{{ $total }} &nbsp;
						
								@if ($previous_id > 0)
									<a href="/passpack/{{ $previous_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-left"></a>
								@endif
								@if ($next_id > 0)
									<a href="/passpack/{{ $next_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-right"></a>
								@endif
							@endif
							
						</div>
						
					</div>
				</div>
				
				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')
	
					<!-- if we are updating a task we need to know the task ID -->
					<input type="hidden" name="passpack_id" value="{{ $passpack->id or '' }}" />
	
					<!-- Passpack URL -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">Name</label>
	
						<div class="col-sm-10">
							<input type="text" name="name" id="passpack-name" class="form-control" value="{{ $passpack->name or old('name') }}">
						</div>
					</div>
	
					<!-- Passpack URL -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">URL</label>
	
						<div class="col-sm-10">
							<input type="text" name="url" id="passpack-url" class="form-control" value="{{ $passpack->url or old('url') }}">
						</div>
					</div>
					
					<!-- Category -->
					<div class="form-group">
						<label for="task-category" class="col-sm-2 control-label">Category</label>
						<div class="col-sm-10">
							<div class="dropdown-category">
							  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							     <span class="selection">--not selected--</span>&nbsp;&nbsp;<span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu" id="dropdown-category" aria-labelledby="dropdownMenu1">
							  	@foreach ($categories as $category)
								    <li js_id="category_{{ $category->id }}"><a href="#" ref="{{ $category->id }}">{{ $category->name }}</a></li>
							    @endforeach
							  </ul>
							</div>
						</div>
						<input type="hidden" id="category" name="category" value="0">
					</div>
					
					
					<script>
	
						//function to show the selected item in the dropdown
						$(".dropdown-category .dropdown-menu li a").click(function(){
							$(this).parents(".dropdown-category").find('.selection').text($(this).text());
							$(this).parents(".dropdown-category").find('.selection').val($(this).text());
							$('#category').val($(this).attr('ref'));
							$('#category_name').val($(this).text());
						});
	
					
						//function to load the saved values
						$old_category="{{ old('category') }}";
						if (!$old_category) {
							$old_category ="{{ $category_id }}";
								if (!$old_category) {
									$old_category ="{{ $passpack->category_id }}";
								}
						}
						if ($old_category > 0) {
							$('#category').val($old_category);
							$old_category_name=$("li[js_id='category_"+$old_category+"']").text();
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').text($old_category_name);
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').val($old_category_name);
						};

						
					</script>
					

					
					<!-- Create/Write -->
					@if ($passpack->id)
					<div class="form-group">
						<label for="task-created" class="col-sm-2 control-label">Create/Write</label>
	
						<div class="col-sm-10" style="padding-top: 7px;">{{ date('d.m.Y G:i:s', strtotime($passpack->created_at)) }} | {{ date('d.m.Y G:i:s', strtotime($passpack->updated_at)) }}</div>
	
					</div>
					@endif
					
					<!-- Passpack User -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">User</label>
	
						<div class="col-sm-10">
							<input type="text" name="passpack_user" autocomplete="off" id="passpack-user" class="form-control" value="{{ $passpack->user or old('passpack_user') }}">
						</div>
					</div>
					
					<!-- Passpack Password -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">Password</label>
	
						<div class="col-sm-10">

							<div class="input-group">
								<span class="input-group-addon">
									<a href="#" id="togglePasswordField" value="Toggle Password"><i class="glyphicon glyphicon-eye-open"></i></a>
								</span>
								
								<input type="password" autocomplete="off" name="passpack_password" id="password" class="form-control" value="{{ $pwd or old('passpack_password') }}">
								@if ($pwd)
								<span class="input-group-addon">
									<a href="#" class="btn-copy" data-clipboard-text="{{ $pwd }}">
								    		<span class="glyphicon glyphicon-copy"></span>
									</a>
								</span>
								@endif
								
							</div>
						</div>
						
					</div>
					
					<!-- Passpack Description -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">Description</label>
	
						<div class="col-sm-10">
							<textarea name="description" id="summernote" class="form-control" >{{ $passpack->description or old('description') }}</textarea>
						</div>
					</div>
					
					<!-- Action Button -->
					<div class="form-group button-group">
						<div class="col-sm-offset-2 col-sm-9">
							<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">
								<i class="glyphicon glyphicon-floppy-save"></i> Save&nbsp;
							</button>
							
							<input type="hidden" name="save_edit_hidden" id="save-edit-hidden" value=""/>
							
							<button type="submit" name="save_edit" class="btn btn-info" value="save_edit" style="margin-bottom: 5px;">
								<i class="glyphicon glyphicon-floppy-saved"></i> Save&Edit&nbsp;
							</button>
							
							<a href="/passpacks?page={{ $page }}" class="btn btn-warning" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
							
							@if ($passpack->id)
							<nobr>
								<a href="/passpack/{{ $passpack->id }}/delete" class="delete btn btn-danger" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-remove"></i> Delete</a>
							</nobr>		
							@endif
							
						</div>
					</div>
						
				</div>
			</div>
		</form>
	</div>
	
	<script>
	
		//set cursor to the task name field
		$(function () {
			$('#passpack-name').focus();
		});

		(function() {

			try {

				// switch the password field to text, then back to password to see if it supports
				// changing the field type (IE9+, and all other browsers do). then switch it back.
				var passwordField = document.getElementById('password');
				passwordField.type = 'text';
				passwordField.type = 'password';
				
				// if it does support changing the field type then add the event handler and make
				// the button visible. if the browser doesn't support it, then this is bypassed
				// and code execution continues in the catch() section below
				var togglePasswordField = document.getElementById('togglePasswordField');
				togglePasswordField.addEventListener('click', togglePasswordFieldClicked, false);
				togglePasswordField.style.display = 'inline';
				
			}
			catch(err) {

			}

		})();

		function togglePasswordFieldClicked() {

			var passwordField = document.getElementById('password');
			var value = passwordField.value;

			if(passwordField.type == 'password') {
				passwordField.type = 'text';
			}
			else {
				passwordField.type = 'password';
			}
			
			passwordField.value = value;

		} 

		shortcut.add("Ctrl+s",function() { $( "#myform" ).submit(); });
		shortcut.add("Ctrl+e",function() { $( "#save-edit-hidden" ).val('save_edit'); $( "#myform" ).submit(); });

	</script>
	
	<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.5/clipboard.min.js"></script>
	<script>
		new Clipboard('.btn-copy');
	</script>
	
@endsection
