<!DOCTYPE html>
<html>
	<head>
		<title>@yield('page_title') | Coanda CMS</title>

		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />
		<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="{{ asset('packages/coanda/summernote/summernote.css') }}">
		<link rel="stylesheet" href="{{ asset('packages/coanda/css/coanda.css') }}">

	</head>
<body>

@if (Coanda::isLoggedIn())
	@include('coanda::admin.includes.header')
@endif

<div class="container-fluid">
	@yield('content')
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="{{ asset('packages/coanda/summernote/summernote.min.js') }}"></script>
<script src="{{ asset('packages/coanda/js/coanda.js') }}"></script>

@yield('footer')

</body>
</html>