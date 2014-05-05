@extends('coanda::admin.layout.main')

@section('page_title', 'Existing drafts for layout block')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
			<li><a href="{{ Coanda::adminUrl('layout/block-view/' . $block->id) }}">{{ $block->name }}</a></li>
			<li>Existing drafts</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Existing drafts for layout block <small><i class="fa fa-th-large"></i></small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="edit-container">

			<div class="alert alert-warning">
				Drafts already exist for this layout block
			</div>

			<div class="row">
				<div class="col-md-12">

					<table class="table table-striped">
						@foreach ($block->drafts() as $version)
							<tr>
								<td class="tight">#{{ $version->version }}</td>
								<td>Last updated {{ $version->present()->updated_at }}</td>
								<td class="tight">
									@if ($version->status == 'draft')
										<a href="{{ Coanda::adminUrl('layout/block-editversion/' . $block->id . '/' . $version->version) }}"><i class="fa fa-pencil-square-o"></i></a>
									@endif
								</td>
							</tr>
						@endforeach
					</table>

					{{ Form::open(['url' => Coanda::adminUrl('layout/block-existing-drafts/' . $block->id)]) }}
						{{ Form::button('Just create a new version for me', ['name' => 'new_version', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
						<a class="btn btn-default" href="{{ Coanda::adminUrl('layout/block-view/' . $block->id) }}">Cancel</a>
					{{ Form::close() }}
				</div>
			</div>

		</div>

	</div>
</div>

@stop