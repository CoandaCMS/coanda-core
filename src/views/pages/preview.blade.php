<!DOCTYPE html>
<html>
	<head>
		<title>Preview #{{ $version->version }} of page #{{ $version->page_id }} | Coanda CMS</title>

		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="{{ asset('packages/coanda/css/preview.css') }}">
	</head>
<body>

<div id="overlay">
	<div id="overlay-inner">
		<p>Version #{{ $version->version }} of page #{{ $version->page_id }}</p>
	</div>
</div>

<iframe id="preview" src="{{ url('pages/render-preview/' . $preview_key) }}"></iframe>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script type="text/javascript">
</script>

</body>
</html>