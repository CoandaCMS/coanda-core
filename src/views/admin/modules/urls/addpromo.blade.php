@extends('coanda::admin.layout.main')

@section('page_title', 'Add Promo URL')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('urls') }}">Urls</a></li>
			<li>Add promo url</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Add Promo Url</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#add" data-toggle="tab">Add new promo URL</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="add">

					{{ Form::open(['url' => Coanda::adminUrl('urls/add-promo')]) }}

						<div class="form-group @if (isset($invalid_fields['from'])) has-error @endif">
							<label class="control-label" for="from_url">From</label>
					    	<input type="text" class="form-control" id="from_url" name="from_url" value="{{ Input::old('from_url') }}">

						    @if (isset($invalid_fields['from']))
						    	<span class="help-block">{{ $invalid_fields['from'] }}</span>
						    @endif
						</div>

						<div class="form-group @if (isset($invalid_fields['to'])) has-error @endif">
							<label class="control-label" for="to_url">To</label>
					    	<input type="text" class="form-control" id="to_url" name="to_url" value="{{ Input::old('to_url') }}">

						    @if (isset($invalid_fields['to']))
						    	<span class="help-block">{{ $invalid_fields['to'] }}</span>
						    @endif
						</div>

						{{ Form::button('Add', ['name' => 'add_promo_url', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}

					{{ Form::close() }}

				</div>
			</div>
		</div>
	</div>
</div>

@stop
