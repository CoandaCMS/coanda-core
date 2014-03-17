<!DOCTYPE html>
<html>
	<head>
		<title>@yield('page_title')</title>

		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="{{ asset('packages/coanda/css/coanda.css') }}">

	</head>
<body>

<div class="container">
	@yield('content')
</div>

<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</body>
</html>