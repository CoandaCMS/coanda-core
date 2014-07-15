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
			Edit layout block
			<small>
				{{ $block->block_type->name() }}
			</small>
		</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>


{{ Form::open(['url' => Coanda::adminUrl('layout/edit-block/' . $block->id), 'files' => true]) }}
<div class="edit-container">

	@if (Session::has('error'))
		<div class="alert alert-danger">
			Error
		</div>
	@endif

	<div class="row">
		<div class="col-md-12">

			<ul class="nav nav-tabs">
				<li class="active"><a href="#attributes" data-toggle="tab">Content</a></li>
			</ul>

			<div class="tab-content edit-container">
				<div class="tab-pane active" id="attributes">

					<div class="form-group @if (isset($invalid_fields['name'])) has-error @endif">
						<label class="control-label" for="name">Name</label>

						{{ Form::text('name', Input::old('name', $block->name), [ 'class' => 'form-control' ]) }}

					    @if (isset($invalid_fields['name']))
					    	<span class="help-block">{{ $invalid_fields['name'] }}</span>
					    @endif
					</div>

					@foreach ($block->attributes() as $attribute)

						@include($attribute->type->edit_template(), ['old_input' => (isset($old_attribute_input[$attribute->identifier]) ? $old_attribute_input[$attribute->identifier] : false), 'is_required' => $attribute->required, 'attribute_identifier' => $attribute->identifier, 'attribute_name' => $attribute->name, 'prefill_data' => $attribute->content, 'invalid_fields' => isset($invalid_fields['attributes']) ? $invalid_fields['attributes'] : []])

					@endforeach

				</div>

			</div>

			<div class="buttons">
				{{ Form::button('Update', ['name' => 'add', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				{{ Form::button('Cancel', ['name' => 'cancel', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
			</div>

		</div>
	</div>
</div>
{{ Form::close() }}

@stop
