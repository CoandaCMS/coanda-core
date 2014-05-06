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

	@if ($trashed_parents->count() > 0)
		<h2>Parent pages to be restored</h2>

		<p>The following parent pages will also need to be restored</p>

		<ul>

			@foreach ($trashed_parents as $trashed_parent)

				<li>{{ $trashed_parent->present()->name }}</li>

			@endforeach
		</ul>

	@endif

	@if ($page->children->count() > 0)
		<h2>Sub pages</h2>
		<p>Would you also like to restore sub pages?</p>

		<div class="checkbox">
			<label for="restore_sub_pages">
				<input type="checkbox" id="restore_sub_pages" name="restore_sub_pages" value="yes">
				Yes, restore all sub pages as well
			</label>
		</div>
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
