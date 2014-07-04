@if (count($content) > 0)
<ul>
	@foreach ($content as $item)
		<li>{{ $item }}</li>
	@endforeach
</ul>
@endif
