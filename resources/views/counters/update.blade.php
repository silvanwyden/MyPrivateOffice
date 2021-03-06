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
	
		<form action="/counter" method="POST" class="form-horizontal" id="myform">
			{{ csrf_field() }}
	
			<div class="row" style="padding-bottom: 15px;">
				<div class="col-sm-8">
				  <div class="btn-group" role="group" aria-label="first">
				  
				  		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
				  		<button type="submit" class="btn btn-info" name="save_edit" value="save_edit" ><span class="glyphicon glyphicon-floppy-saved"></span> Save&Edit</button>
			  			<a href="/counters?page={{ $page }}" class="btn btn-default"><span class="glyphicon glyphicon-home"></span></a>
				  		
					</div>
				</div>
			</div>
	
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
					
						<div class="col-sm-10">
							Counter
						</div>
					
						<div class="col-sm-2" style="text-align: right;">
					
							@if ($cnt > 0)
								{{ $cnt }}/{{ $total }} &nbsp;
						
								@if ($previous_id > 0)
									<a href="/counter/{{ $previous_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-left"></a>
								@endif
								@if ($next_id > 0)
									<a href="/counter/{{ $next_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-right"></a>
								@endif
							@endif
							
						</div>
						
					</div>
				</div>
	
				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')

					<!-- if we are updating a task we need to know the task ID -->
					<input type="hidden" name="counter_id" value="{{ $counter->id or '' }}" />

					<!-- Counter Date -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">Date</label>

						<div class="col-sm-10">
							@if ($counter->date > 0)
								<input type="text" name="date" id="datepicker" class="form-control" value="{{ date('d.m.Y', strtotime($counter->date)) }}">
							@else
								<input type="text" name="date" id="datepicker" class="form-control" value="{{ date('d.m.Y', time()) }}">
							@endif
						</div>
					</div>
					
					<!-- Category -->
					<div class="form-group">
						<label for="counter-category" class="col-sm-2 control-label">Category</label>
						<div class="col-sm-10">
							<div class="dropdown-category">
							  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							     <span class="selection">--not selected--</span>&nbsp;&nbsp;<span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu" id="dropdown-category" aria-labelledby="dropdownMenu1">
							  	@foreach ($countercategories as $category)
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
							$old_category ="{{ $counter_category_id }}";
								if (!$old_category) {
									$old_category ="{{ $counter->counter_category_id }}";
								}
						}
						if ($old_category > 0) {
							$('#category').val($old_category);
							$old_category_name=$("li[js_id='category_"+$old_category+"']").text();
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').text($old_category_name);
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').val($old_category_name);
						};

					</script>
					
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
							
							<a href="/counters?page={{ $page }}" class="btn btn-warning" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
							
							@if ($counter->id)
							<nobr>
								<a href="/counter/{{ $counter->id }}/delete" class="delete btn btn-danger" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-remove"></i> Delete</a>
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
			$('#counter-date').focus();
		});

		shortcut.add("Ctrl+s",function() { $( "#myform" ).submit(); });
		shortcut.add("Ctrl+e",function() { $( "#save-edit-hidden" ).val('save_edit'); $( "#myform" ).submit(); });

	</script>
	
@endsection
