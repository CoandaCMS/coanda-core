@extends('coanda::admin.layout.main')

@section('page_title', 'Layout')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layout</a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Layout</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#layouts" data-toggle="tab">Layouts</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="layouts">
					@if (count($layouts) > 0)
						<table class="table table-striped">
							@foreach ($layouts as $layout)
								<tr>
									<td><a href="{{ Coanda::adminUrl('layout/view/' . $layout->identifier()) }}"><i class="fa fa-code"></i> {{ $layout->name() }}</a></td>
								</tr>
							@endforeach
						</table>
					@else
						<p>No layouts have been specified.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@stop