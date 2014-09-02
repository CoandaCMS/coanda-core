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
		<h1 class="pull-left">Redirect Urls</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $urls->getTotal() }}</span>
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

					@if (Session::has('removed'))
						<div class="alert alert-danger">
							Redirect removed
						</div>					
					@endif
					
					@if ($urls->count() > 0)
						<table class="table table-striped">
							@foreach ($urls as $url)
								<tr>
									<td>
										{{ $url->from_url }}

										<a href="{{ url($url->from_url) }}" class="new-window"><i class="fa fa-external-link"></i></a>
									</td>
									<td>{{ $url->destination }}</td>
									<td>{{ $url->counter }} hit{{ $url->counter != 1 ? 's' : ''}}</td>
									<td><a href="{{ Coanda::adminUrl('urls/remove-redirect/' . $url->id) }}"><i class="fa fa-minus-circle"></i></a></td>
								</tr>
							@endforeach
						</table>

						{{ $urls->links() }}
					@else
						<p>No redirect urls have been added.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#add" data-toggle="tab">Add new redirect URL</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="add">

					{{ Form::open(['url' => Coanda::adminUrl('urls/add-redirect')]) }}

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
