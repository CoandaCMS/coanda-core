@extends('coanda::admin.layout.main')

@section('page_title', 'Layout: ' . $layout->name())

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
			<li>{{ $layout->name() }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $layout->name() }} <small><i class="fa fa-code"></i></small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#regions" data-toggle="tab">Regions</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="regions">

					@if ($layout->regionCount() > 0)

						<table class="table table-striped">

							@foreach ($layout->regions() as $region)
								<tr>
									<td>
										<a href="{{ Coanda::adminUrl('layout/region/' . $layout->identifier() . '/' . $region->identifier()) }}">{{ $region->name() }}</a>
									</td>
								</tr>

							@endforeach

						</table>

					@else
						<p>This layout doesn't have any editable regions.</p>
					@endif

				</div>
			</div>
		</div>

	</div>
</div>

@stop