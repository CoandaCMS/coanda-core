@extends('coanda::admin.layout.main')

@section('page_title', 'Layout: ' . $layout->name() . ' / ' . $region_name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layout</a></li>
			<li><a href="{{ Coanda::adminUrl('layout/view/' . $layout->identifier()) }}">{{ $layout->name() }}</a></li>
			<li>{{ $region_name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			{{ $layout->name() }} / {{ $region_name }}
		</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#regions" data-toggle="tab">Modules</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="regions">

					@if (count($module_identifiers) > 0)
						<table class="table table-striped">
							@foreach ($module_identifiers as $module)
								<tr>
									<td><a href="{{ Coanda::adminUrl('layout/module/' . $layout->identifier() . '/' . $region_identifier . '/' . $module->module_identifier) }}"><i class="fa fa-something"></i> {{ $module->module_identifier }}</a></td>
									<td class="tight">{{ $module->block_count }} {{ $module->block_count == 1 ? 'block' : 'blocks' }}</td>
								</tr>
							@endforeach
						</table>
					@else
						<p>This regions doesn't have any module assignments specified.</p>
					@endif

				</div>
			</div>
		</div>
	</div>
</div>

@stop