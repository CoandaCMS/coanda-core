@extends('coanda::admin.layout.main')

@section('page_title', 'Layout Block ' . $block->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layout blocks</a></li>
			<li>{{ $block->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			{{ $block->name }}
			<small>
				{{ $block->block_type->name() }}
			</small>
		</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<div class="btn-group">
			<a href="{{ Coanda::adminUrl('layout/edit-block/' . $block->id) }}" class="btn btn-primary">Edit</a>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#assignments" data-toggle="tab">Regions</a></li>
				<li><a href="#content" data-toggle="tab">Content</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="assignments">

					@if (Session::has('page_saved'))
						<div class="alert alert-success">
							Added
						</div>
					@endif

					@if ($region_assigments->count() > 0)
						<table class="table table-striped">
							<tr>
								<th>Layout</th>
								<th>Region</th>
								<th>Module Identifier</th>
								<th>Cascade</th>
								<th></th>
							</tr>
							@foreach ($region_assigments as $region_assigment)
								<tr>
									<td>{{ $region_assigment->layout_identifier }}</td>
									<td>{{ $region_assigment->region_identifier }}</td>
									<td>{{ $region_assigment->module_identifier }}</td>
									<td>{{ $region_assigment->cascade }}</td>
									<td><a href="{{ Coanda::adminUrl('layout/remove-assignment/' . $region_assigment->id) }}">remove</a></td>
								</tr>
							@endforeach
						</table>

						{{ $region_assigments->links() }}
					@else
						<p>This block has not been assigned to any layout regions yet.</p>
					@endif

				</div>
				<div class="tab-pane" id="content">
					<table class="table table-striped">
						<tr>
							<td class="tight">Name</td>
							<td>{{ $block->name }}</td>
						</tr>
						@foreach ($block->attributes as $attribute)
						<tr>
							<td class="tight">{{ $attribute->name }}</td>
							<td>
								@include($attribute->type->view_template(), [ 'attribute_definition' => $attribute->definition, 'content' => $attribute->content ])
							</td>
						</tr>
						@endforeach
					</table>
				</div>

			</div>
		</div>
	</div>

	<div class="col-md-4">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#newassignment" data-toggle="tab">Add to region</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="newassignment">
					{{ Form::open(['url' => Coanda::adminUrl('layout/block/' . $block->id)]) }}
						<div class="form-group">
							<label>Layout/Region</label>
							<select name="layout_region_identifier" class="form-control">
								@foreach (Coanda::layout()->layouts() as $layout)
									@foreach ($layout->regions() as $region_identifier => $region)
										<option value="{{ $layout->identifier() }}:{{ $region_identifier }}">{{ $layout->name() }} / {{ $region['name'] }}</option>
									@endforeach
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>Module identifier</label>
							<input type="text" name="module_identifier" class="form-control" placeholder="Module identifier e.g. pages:2 or default">
						</div>

						<div class="form-group">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="cascade">
									Cascade
								</label>
							</div>
						</div>

						<button type="submit" class="btn btn-primary">Add</button>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
</div>

@stop