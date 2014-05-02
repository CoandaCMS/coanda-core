@extends('coanda::admin.layout.main')

@section('page_title', 'Edit layout block')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layout</a></li>
			<li>Edit layout block</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">
			Edit layout block (version #{{ $version->version }})
			<small>
				<i class="fa"></i>
				{{ $version->block->type }}
			</small>
		</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('layout/block-editversion/' . $version->layout_block_id . '/' . $version->version)]) }}
<div class="edit-container">

	@if (Session::has('saved'))
		<div class="alert alert-success">
			Saved
		</div>
	@endif

	@if (Session::has('error'))
		<div class="alert alert-danger">
			Error
		</div>
	@endif

	<div class="row">
		<div class="col-md-8">

			<ul class="nav nav-tabs">
				<li class="active"><a href="#attributes" data-toggle="tab">Content</a></li>
			</ul>

			<div class="tab-content edit-container">
				<div class="tab-pane active" id="attributes">
					@foreach ($version->attributes as $attribute)

						@include($attribute->type()->edit_template(), [ 'attribute_identifier' => $attribute->id, 'attribute_name' => $attribute->name, 'invalid_fields' => $invalid_fields, 'is_required' => $attribute->is_required, 'prefill_data' => $attribute->type_data ])

					@endforeach
				</div>

			</div>

			<div class="buttons">
				{{ Form::button('Save', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Save & Exit', ['name' => 'save_exit', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Discard draft', ['name' => 'discard', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
				{{ Form::button('Publish', ['name' => 'publish', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-success']) }}
			</div>

		</div>
		<div class="col-md-4">

			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab">Details</a></li>
			</ul>

			<div class="edit-container tab-content">

				<div class="tab-pane active" id="details">

					{{--
					<div class="row">
						<input type="hidden" name="date_format" value="d/m/Y H:i">
						<div class="col-md-6">
							<div class="form-group @if (isset($invalid_fields['visible_from'])) has-error @endif">
								<label class="control-label" for="visibility">Visibile from</label>
								<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY HH:mm">
									<input type="text" class="date-field form-control" id="visible_from" name="visible_from" value="{{ Input::old('visible_from', $version->present()->visible_from) }}">
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
								</div>
							    @if (isset($invalid_fields['visible_from']))
							    	<span class="help-block">{{ $invalid_fields['visible_from'] }}</span>
							    @endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group @if (isset($invalid_fields['visible_to'])) has-error @endif">
								<label class="control-label" for="visibility">Visibile to</label>
								<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY HH:mm">
									<input type="text" class="date-field form-control" id="visible_to" name="visible_to" value="{{ Input::old('visible_to', $version->present()->visible_to) }}">
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
								</div>
							    @if (isset($invalid_fields['visible_to']))
							    	<span class="help-block">{{ $invalid_fields['visible_to'] }}</span>
							    @endif
							</div>
						</div>
					</div>
					--}}

				</div>

			</div>

		</div>
	</div>
</div>
{{ Form::close() }}

@stop
