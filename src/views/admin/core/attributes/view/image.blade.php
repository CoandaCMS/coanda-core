@if ($content)
	<img src="{{ $content->present()->thumbnail_url }}" class="img-thumbnail">
@endif