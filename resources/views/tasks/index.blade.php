@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<!-- Current Tasks -->
			
			<a href="/task" class="btn btn-primary">Create New Task</a><br><br>
			
			@if (count($tasks) > 0)
				<div class="panel panel-default">
					<div class="panel-heading">
						Current Tasks
					</div>

					<div class="panel-body">
						<table class="table table-striped task-table" id="clickable">
							<thead>
								<th>Task</th>
								<th>Category</th>
								<th>Priority</th>
								<th>Deadline</th>
								<th>State</th>
								<th>Action</th>
							</thead>
							<tbody>
								@foreach ($tasks as $task)
									<tr>
										<!-- td class="table-text"><div>{{ $task->id }}</div></td-->
										<td class="table-text">
											<a href="/task/{{ $task->id }}/update">
												<div>{{ $task->name }}</div>
											</a>
										</td>
										<td class="table-text"><div class="btn {{ $task->category['css_class'] }}">{{ $task->category['name'] }}</div></td>
										<td class="table-text"><div>{{ $task->priority_id }}</div></td>
										<td class="table-text"><div>{{ date('d.m.Y', strtotime($task->deadline)) }}</div></td>
										<td class="table-text"><div>{{ $task->state_id }}</div></td>
										
										<!-- Task Delete Button -->
										<td>
											<form action="/task/{{ $task->id }}" method="POST">
												{{ csrf_field() }}
												{{ method_field('DELETE') }}

												<button type="submit" id="delete-task-{{ $task->id }}" class="btn btn-danger">
													<i class="fa fa-btn fa-trash"></i>
												</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{!! $tasks->appends(['sort' => 'name'])->render() !!}
					</div>
			@endif
		</div>
	</div>
@endsection
