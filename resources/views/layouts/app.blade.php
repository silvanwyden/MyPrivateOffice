<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>MyPrivateOffice</title>

	<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,700" rel="stylesheet" type="text/css">
	<link href="/custom.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

	<!-- include libraries(jQuery, bootstrap, fontawesome) -->
	<link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet"> 
	<link href="https://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script> 
	<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
	
	<!-- include summernote css/js-->
	<link href="/summernote.css" rel="stylesheet">
	<script src="/summernote.js"></script>

	<style>
		body {
			font-family: 'Raleway';
			margin-top: 25px;
		}

		button .fa {
			margin-right: 6px;
		}

		.table-text div {
			padding-top: 6px;
		}
	</style>

	<script>
		$(function () {
			$('#task-name').focus();
		});
	</script>
</head>

<body>
	<div class="container">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

					<a class="navbar-brand" href="/tasks">My Tasks</a>
				</div>

				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						&nbsp;
					</ul>
					
					<ul class="nav navbar-nav navbar-right">
						@if (Auth::guest())
							<li><a href="/auth/register"><i class="fa fa-btn fa-heart"></i>Register</a></li>
							<li><a href="/auth/login"><i class="fa fa-btn fa-sign-in"></i>Login</a></li>
						@else
							<li class="navbar-text"><i class="fa fa-btn fa-user"></i>{{ Auth::user()->name }}</li>
							<li><a href="/auth/logout"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
						@endif
					</ul>
					
				</div>
			</div>
		</nav>
	</div>

	@yield('content')
	
		  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
		  <script>
		  $(function() {
			  $( "#datepicker" ).datepicker({ dateFormat: 'd.m.yy' });
		  });

		  $(".delete").click(function(){
		        return confirm("Do you want to delete this item?");
		    });
		  
		  $(document).ready(function() {

			  $('#clickable .table-text').click(function() {
				  window.location.href = $(this).parent().find("a").attr("href");
				});

			  
			  $('#summernote').summernote({
				  height: 300,
				  toolbar: [
					['style', ['bold', 'italic', 'underline', 'strikethrough']],
					['para', ['ul', 'ol',]],
					['misc1', ['link', 'picture', 'table', 'codeview', 'fullscreen']],
							  ],
			  });

		      $('#submitbutton').hide();
			  
			});


	  </script>
	
</body>
</html>
