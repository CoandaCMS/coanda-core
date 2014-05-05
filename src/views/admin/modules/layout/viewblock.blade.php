@extends('coanda::admin.layout.main')

@section('page_title', 'View layout block: ' . $block->present()->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
			<li>{{ $block->present()->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			{{ $block->present()->name }}
			<small>
				@if ($block->is_draft)
					<i class="fa fa-circle-o"></i>
				@else
					<i class="fa fa-th-large"></i>
				@endif
				{{ $block->blockType()->name() }}
			</small>
		</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Version {{ $block->current_version }}</span>

			<span class="label @if ($block->is_draft) label-warning @else label-success @endif">{{ $block->present()->status }}</span>
		</div>
	</div>
</div>

@if ($block->visible_from || $block->visible_to)
<div class="row">
	<div class="page-visibility col-md-12">
		@if ($block->is_visible)
			<span class="label label-success">Visible</span>
		@else
			<span class="label label-info">Hidden</span>
		@endif
		<i class="fa fa-calendar"></i> {{ $block->present()->visible_dates }}
	</div>
</div>
@endif

<div class="row">
	<div class="page-options col-md-12">

		<div class="row">
			<div class="col-md-12">
				<div class="btn-group">

					@if ($block->is_draft)
						<a href="{{ Coanda::adminUrl('layout/block-editversion/' . $block->id . '/1') }}" class="btn btn-primary">Continue editing</a>
					@else
						<a href="{{ Coanda::adminUrl('layout/block-edit/' . $block->id) }}" class="btn btn-primary">Edit</a>
					@endif
					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							More
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li>
								<a href="{{ Coanda::adminUrl('layout/block-delete/' . $block->id) }}">Delete</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#content" data-toggle="tab">Content</a></li>
				<li><a href="#versions" data-toggle="tab">Versions ({{ $block->versions->count() }})</a></li>
			</ul>
			<div class="tab-content">

				<div class="tab-pane active" id="content">

					<table class="table table-striped">
						@foreach ($block->attributes as $attribute)
						<tr>
							<td class="tight">{{ $attribute->name }}</td>
							<td>
								@include($attribute->type()->view_template(), [ 'content' => $attribute->type_data ])
							</td>
						</tr>
						@endforeach
					</table>

				</div>
				<div class="tab-pane" id="versions">

					<table class="table table-striped">
						@foreach ($block->versions as $version)
							<tr>
								<td class="tight">
									@if ($version->status == 'draft')
										<a href="{{ Coanda::adminUrl('layout/block-removeversion/' . $block->id . '/' . $version->version) }}"><i class="fa fa-minus-circle"></i></a>
									@else
										<i class="fa fa-minus-circle fa-disabled"></i>
									@endif
								</td>
								<td>#{{ $version->version }}</td>
								<td>
									Updated: {{ $version->present()->updated_at }}

									@if ($version->status == 'draft')
										<span class="label label-warning">
									@elseif ($version->status == 'published')
										<span class="label label-success">
									@else  
										<span class="label label-default">
									@endif

									{{ $version->present()->status }}</span>
								</td>
								<td class="tight">
									@if ($version->status == 'draft')
										<a href="{{ Coanda::adminUrl('layout/block-editversion/' . $block->id . '/' . $version->version) }}"><i class="fa fa-pencil-square-o"></i></a>
									@endif
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
				<li class="active"><a href="#layouts" data-toggle="tab">Layout regions</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="layouts">

					@if (Session::has('region_added'))
						<div class="alert alert-success">
							Block added to region
						</div>
					@endif

					@if (Session::has('region_removed'))
						<div class="alert alert-danger">
							Block removed from region
						</div>
					@endif

					@if ($version->block->defaultRegions()->count() > 0)
						<table class="table table-striped">
							@foreach ($version->block->defaultRegions() as $region)
								<tr>
									<td class="tight"><a href="{{ Coanda::adminUrl('layout/remove-block-from-region/' . $block->id . '/' . $region->layout->identifier() . '/' . $region->region->identifier()) }}"><i class="fa fa-minus-circle"></i></a></td>
									<td><a href="{{ Coanda::adminUrl('layout/view-region/' . $region->layout->identifier() . '/' . $region->region->identifier()) }}">{{ $region->layout->name() }}/{{ $region->region->name() }}</a></td>
								</tr>
							@endforeach
						</table>
					@else
						<p>This block is not used in any layout regions.</p>
					@endif

					@if (count($available_regions) > 0)
						{{ Form::open([ 'url' => Coanda::adminUrl('layout/block-view/' . $block->id), 'class' => 'form-inline' ]) }}
							<select name="add_region" class="form-control">
								@foreach ($available_regions as $available_region)
									<option value="{{ $available_region['identifier'] }}">{{ $available_region['name'] }}</option>
								@endforeach
							</select>
							{{ Form::button('Add', ['name' => 'add_default', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
						{{ Form::close() }}
					@endif

				</div>
			</div>
		</div>
	</div>
</div>

@stop
