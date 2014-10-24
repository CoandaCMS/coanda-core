@extends('coanda::admin.layout.main')

@section('page_title', 'Restore ' . $page->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>Restore {{ $page->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Restore "{{ $page->name }}" <small>{{ $page->type_name }}</small></h1>
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

	<table class="table table-striped">
		<tr>
			<td>
				@foreach ($page->parents() as $parent)
					{{ $parent->name }} @if ($page->is_trashed) * @endif /
				@endforeach
				{{ $page->name }}
			</td>
			<td>
				<input type="checkbox" id="restore_sub_pages" name="restore_sub_pages[]" value="yes" checked="checked">
				Also restore sub pages
			</td>
		</tr>
	</table>

	<p><i class="fa fa-exclamation-circle"></i> Parent pages marked with a * will also be restored.</p>

	<div class="row">
		<div class="col-md-12">
			{{ Form::button('Yes, please restore', ['name' => 'confirm_restore', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
			<a class="btn btn-default" href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">Cancel</a>
		</div>
	</div>

</div>
{{ Form::close() }}
@stop
