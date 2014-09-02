<!DOCTYPE html>
<html>
	<head>
		<title>@yield('page_title') | Coanda CMS</title>
		<meta charset="utf-8">

		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700' type="text/css">
		<link rel="stylesheet" href="{{ asset('packages/coandacms/coanda-core/summernote/summernote.css') }}">
		<link rel="stylesheet" href="{{ asset('packages/coandacms/coanda-core/datepicker/bootstrap-datetimepicker.min.css') }}">
		<link rel="stylesheet" href="{{ asset('packages/coandacms/coanda-core/dropzone/css/dropzone.css') }}">
		<link rel="stylesheet" href="{{ asset('packages/coandacms/coanda-core/css/coanda.css') }}">

		<style type="text/css">
			@include('coanda::admin.css.colours')
		</style>

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
<script src="{{ asset('packages/coandacms/coanda-core/summernote/summernote.js') }}"></script>
<script src="{{ asset('packages/coandacms/coanda-core/js/jquery.slugify.js') }}"></script>
<script src="{{ asset('packages/coandacms/coanda-core/js/moment.min.js') }}"></script>
<script src="{{ asset('packages/coandacms/coanda-core/datepicker/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ asset('packages/coandacms/coanda-core/dropzone/dropzone.min.js') }}"></script>

<script src="{{ asset('packages/coandacms/coanda-core/js/coanda.js') }}"></script>

@yield('custom-js')

@yield('footer')

</body>
</html>