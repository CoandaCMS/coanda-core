@if ($content)
	<img src="{{ url($content->resizeUrl(150)) }}" class="img-thumbnail">
@endif