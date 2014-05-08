@extends('coanda::admin.layout.main')

@section('page_title', 'Restore ' . $page->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>Restore {{ $page->present()->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Restore "{{ $page->present()->name }}" <small>{{ $page->present()->type }}</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('pages/restore/' . $page->id)]) }}
<div class="edit-container">

	<div class="alert alert-warning">
		<i class="fa fa-exclamation-triangle"></i> Are you sure you want to restore this page?
	</div>

	@if ($page->locations->count() > 0)

		<table class="table table-striped">
			@foreach ($page->locations as $location)
				<tr>
					<td>
						<strong>Location:</strong>
						@foreach ($location->parents() as $parent)
							{{ $parent->page->present()->name }} @if ($parent->page->is_trashed) * @endif /
						@endforeach
						{{ $location->page->present()->name }}
					</td>
					<td>
						<input type="checkbox" id="restore_sub_pages_location_{{ $location->id }}" name="restore_sub_pages[]" value="{{ $location->id }}">
						Also restore sub pages
					</td>
				</tr>
			@endforeach
		</table>

		<p><i class="fa fa-exclamation-circle"></i> Parent pages marked with a * will also be restored.</p>
	@endif

	<div class="row">
		<div class="col-md-12">
			{{ Form::button('Yes, please restore', ['name' => 'confirm_restore', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
			<a class="btn btn-default" href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">Cancel</a>
		</div>
	</div>

</div>
{{ Form::close() }}
@stop
