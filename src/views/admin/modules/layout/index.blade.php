@extends('coanda::admin.layout.main')

@section('page_title', 'Layouts')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Layouts</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ count($layouts) }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#pages" data-toggle="tab">Layouts</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="subpages">

					{{ Form::open(['url' => Coanda::adminUrl('pages')]) }}

						@if (count($layouts) > 0)
							<table class="table table-striped">
								@foreach ($layouts as $layout)
									<tr>
										<td>
											<i class="fa fa-code"></i>
											<a href="{{ Coanda::adminUrl('layout/view/' . $layout->identifier()) }}">{{ $layout->name() }}</a>
										</td>
										<td>{{ $layout->regionCount() }} editable regions</td>
									</tr>
								@endforeach
							</table>
						@else
							<p>No layouts have been specified!</p>
						@endif

					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
</div>

@stop