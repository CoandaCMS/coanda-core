@extends('coanda::admin.layout.main')

@section('page_title', 'Browse available blocks')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li>Layouts</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Browse available blocks</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('layout/page-custom-region-block/' . $page_id . '/' . $version_number . '/' . $layout_identifier . '/' . $region_identifier)]) }}
<div class="row">
	<div class="col-md-12">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#blocks" data-toggle="tab">Blocks</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="blocks">

					@if ($block_list->count() > 0)
						<table class="table table-striped">
							@foreach ($block_list as $block)
								<tr class="status-{{ $block->status }}">
									<td><input @if ($block->is_draft) disabled="disabled" @endif type="checkbox" name="add_block_list[]" value="{{ $block->id }}"></td>
									<td><i class="fa fa-th-large"></i> {{ $block->present()->name }}</td>
									<td>{{ $block->blockType()->name() }}</td>
								</tr>
							@endforeach
						</table>

						{{ $block_list->links() }}

						{{ Form::button('Add selected', ['name' => 'add_selected', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
					@else
						<p>No layout blocks have been created.</p>
					@endif

				</div>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}

@stop