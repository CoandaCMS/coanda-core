@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm deletion of ' . $page->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>{{ $page->present()->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $page->present()->name }} <small>{{ $page->present()->type }}</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="edit-container">

	<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> Are you sure you want to remove this page?
	</div>

	@if ($page->locations->count() > 0)

		<p><i class="fa fa-info-circle"></i> The page will be removed from the following locations.</p>

		<table class="table table-striped">
			@foreach ($page->locations as $location)
				<tr>
					<td>
						@foreach ($location->parents() as $parent)
							{{ $parent->page->present()->name }}</a> /
						@endforeach

						{{ $location->page->present()->name }}
					</td>
					<td>
						{{ $location->subTreeCount() }} sub page{{ $location->subTreeCount() != 1 ? 's' : '' }} will also be deleted.
					</td>
				</tr>
			@endforeach
		</table>

	@endif

	<div class="row">
		<div class="col-md-12">

			{{ Form::open(['url' => Coanda::adminUrl('pages/delete/' . $page->id)]) }}
				{{ Form::button('Yes, I understand', ['name' => 'confirm_delete', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				<a class="btn btn-default" href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">Cancel</a>
			{{ Form::close() }}

		</div>
	</div>

</div>
@stop
