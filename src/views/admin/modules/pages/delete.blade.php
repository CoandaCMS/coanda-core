@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm deletion of ' . $page->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>{{ $page->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $page->name }} <small>{{ $page->type }}</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="edit-container">

	{{ Form::open(['url' => Coanda::adminUrl('pages/delete/' . $page->id)]) }}

		<div class="alert alert-danger">
			<i class="fa fa-exclamation-triangle"></i> Are you sure you want to remove this page?
		</div>

		<table class="table table-striped">
			<tr>
				<td>
					@foreach ($page->parents() as $parent)
						{{ $parent->name }} /
					@endforeach

					{{ $page->name }}
				</td>
				<td>
					{{ $page->subTreeCount() }} sub page{{ $page->subTreeCount() != 1 ? 's' : '' }} will also be deleted.
				</td>
			</tr>
		</table>

		<div class="checkbox">
			<label>
				<input type="checkbox" id="permanent_delete" name="permanent_delete" value="true">
				Delete permanently
			</label>
		</div>

		<div class="row">
			<div class="col-md-12">

				{{ Form::button('Yes, I understand', ['name' => 'confirm_delete', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				<a class="btn btn-default" href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">Cancel</a>

			</div>
		</div>

	{{ Form::close() }}

</div>
@stop
