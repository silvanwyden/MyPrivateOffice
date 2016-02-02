@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					New Task
				</div>

				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')

					<!-- New Task Form -->
					<form action="/task" method="POST" class="form-horizontal">
						{{ csrf_field() }}
						
						
						<input type="hidden" name="task_id" value="{{ $task->id or '' }}" />

						<!-- Task Name -->
						<div class="form-group">
							<label for="task-name" class="col-sm-3 control-label">Task</label>

							<div class="col-sm-8">
								<input type="text" name="name" id="task-name" class="form-control" value="{{ $task->name or old('name') }}">
							</div>
						</div>
						
						<!-- Category -->
						<div class="form-group">
							<label for="task-category" class="col-sm-3 control-label">Category</label>
							<div class="col-sm-8">
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
						
						<!-- Priority -->
						<div class="form-group">
							<label for="task-priority" class="col-sm-3 control-label">Priority</label>
							<div class="col-sm-8">
								<div class="dropdown-priority">
								  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								     <span class="selection">Normal</span>&nbsp;&nbsp;<span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
								  	@foreach ($priorities as $priority)
									    <li js_id="priority_{{ $priority->id }}"><a href="#" ref="{{ $priority->id }}">{{ $priority->name }}</a></li>
								    @endforeach
								  </ul>
								</div>
							</div>
							<input type="hidden" id="priority" name="priority" value="3">
						</div>
						
						<!-- Stage -->
						<div class="form-group">
							<label for="task-priority" class="col-sm-3 control-label">Stage</label>
							<div class="col-sm-8">
								<div class="dropdown-stage">
								  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								     <span class="selection">Open</span>&nbsp;&nbsp;<span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
								  	@foreach ($stages as $stage)
									    <li js_id="stage_{{ $stage->id }}"><a href="#" ref="{{ $stage->id }}">{{ $stage->name }}</a></li>
								    @endforeach
								  </ul>
								</div>
							</div>
							<input type="hidden" id="stage" name="stage" value="1">
						</div>
						
						<script>

							$(".dropdown-category .dropdown-menu li a").click(function(){
								$(this).parents(".dropdown-category").find('.selection').text($(this).text());
								$(this).parents(".dropdown-category").find('.selection').val($(this).text());
								$('#category').val($(this).attr('ref'));
								$('#category_name').val($(this).text());
							});

							$(".dropdown-priority .dropdown-menu li a").click(function(){
								$(this).parents(".dropdown-priority").find('.selection').text($(this).text());
								$(this).parents(".dropdown-priority").find('.selection').val($(this).text());
								$('#priority').val($(this).attr('ref'));
							});

							$(".dropdown-stage .dropdown-menu li a").click(function(){
								$(this).parents(".dropdown-stage").find('.selection').text($(this).text());
								$(this).parents(".dropdown-stage").find('.selection').val($(this).text());
								$('#stage').val($(this).attr('ref'));
							});

							$old_category="{{ $task->category_id or old('category') }}";
							if ($old_category > 0) {
								$('#category').val($old_category);
								$old_category_name=$("li[js_id='category_"+$old_category+"']").text();
								$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').text($old_category_name);
								$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').val($old_category_name);
							};

							$old_priority="{{ $task->priority_id or old('priority') }}";
							if ($old_priority > 0) {
								$('#priority').val($old_priority);
								$old_priority_name=$("li[js_id='priority_"+$old_priority+"']").text();
								$(".dropdown-priority .dropdown-menu li a").parents(".dropdown-priority").find('.selection').text($old_priority_name);
								$(".dropdown-priority .dropdown-menu li a").parents(".dropdown-priority").find('.selection').val($old_priority_name);
							};

							$old_stage="{{ $task->stage_id or old('stage') }}";
							if ($old_stage > 0) {
								$('#stage').val($old_stage);
								$old_stage_name=$("li[js_id='stage_"+$old_stage+"']").text();
								$(".dropdown-stage .dropdown-menu li a").parents(".dropdown-stage").find('.selection').text($old_stage_name);
								$(".dropdown-stage .dropdown-menu li a").parents(".dropdown-stage").find('.selection').val($old_stage_name);
							};
							

						</script>
						
						<!-- Deadline -->
						<div class="form-group">
							<label for="task-name" class="col-sm-3 control-label">Deadline</label>

							<div class="col-sm-8">
								@if ($task->deadline > 0)
									<input type="text" name="deadline" id="datepicker" class="form-control" value="{{ date('d.m.Y', strtotime($task->deadline)) }}">
								@else
									<input type="text" name="deadline" id="datepicker" class="form-control" value="{{ old('deadline') }}">
								@endif
							</div>
						</div>
						
						<!-- Description -->
						<div class="form-group">
							<label for="task-description" class="col-sm-3 control-label">Description</label>

							<div class="col-sm-8">
								<textarea name="description" id="summernote" class="form-control" >{{ $task->description or old('description') }}</textarea>
							</div>
						</div>

						<!-- Add Task Button -->
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-8">
								<button type="submit" class="btn btn-default">
									<i class="fa fa-btn fa-plus"></i>Save Task
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	
@endsection