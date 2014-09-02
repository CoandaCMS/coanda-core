@extends('coanda::admin.layout.main')

@section('page_title', 'Remove redirect url')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('urls') }}">URLs</a></li>
			<li>Remove redirect</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Remove redirect</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#urls" data-toggle="tab">Remove</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="urls">

					<div class="alert alert-danger">
						Are you sure you want to remove this redirect?
					</div>

					<div class="buttons">
						{{ Form::open(['url' => Coanda::adminUrl('urls/remove-redirect/' . $url->id)]) }}
							{{ Form::button('Remove', ['type' => 'submit', 'class' => 'btn btn-primary']) }}
							<a href="{{ Coanda::adminUrl('urls') }}" class="btn btn-default">Cancel</a>
						{{ Form::close() }}
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

@stop
