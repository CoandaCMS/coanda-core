@extends('coanda::admin.layout.main')

@section('page_title', 'Pages')

@section('content')

<div class="container">
	<h1>Here are all your pages!</h1>

	<div class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			Add new <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
			@foreach (Coanda::availablePageTypes() as $page_type)
				<li><a href="{{ Coanda::adminUrl('pages/create/' . $page_type->identifier) }}">{{ $page_type->name }}</a></li>
			@endforeach
		</ul>
	</div>

</div>

@stop
