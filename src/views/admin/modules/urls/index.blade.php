@extends('coanda::admin.layout.main')

@section('page_title', 'Urls')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('urls') }}">Urls</a></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Promo Urls</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $promo_urls->getTotal() }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#urls" data-toggle="tab">Promo Urls</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="urls">
					@if ($promo_urls->count() > 0)
						<table class="table table-striped">
							@foreach ($promo_urls as $promo_url)
								<tr>
									<td>
										{{ $promo_url->from_url }}

										<a href="{{ url($promo_url->from_url) }}" class="new-window"><i class="fa fa-external-link"></i></a>
									</td>
									<td>{{ $promo_url->destination }}</td>
									<td>{{ $promo_url->counter }} hit{{ $promo_url->counter != 1 ? 's' : ''}}</td>
								</tr>
							@endforeach
						</table>

						{{ $promo_urls->links() }}
					@else
						<p>No promotional urls have been added.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#add" data-toggle="tab">Add new promo URL</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="add">

					{{ Form::open(['url' => Coanda::adminUrl('urls/add-promo')]) }}

						<div class="form-group">
							<label class="control-label" for="from_url">From</label>
					    	<input type="text" class="form-control" id="from_url" name="from_url" value="">
						</div>

						<div class="form-group">
							<label class="control-label" for="to_url">To</label>
					    	<input type="text" class="form-control" id="to_url" name="to_url" value="">
						</div>

						{{ Form::button('Add', ['name' => 'add_promo_url', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}

					{{ Form::close() }}

				</div>
			</div>
		</div>
	</div>
</div>

@stop
