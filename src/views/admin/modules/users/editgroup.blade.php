@extends('coanda::admin.layout.main')

@section('page_title', 'Edit user group: ' . $group->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('users') }}">Users</a></li>
			<li>{{ $group->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Edit user group "{{ $group->name }}"</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="edit-container">

			{{ Form::open(['url' => Coanda::adminUrl('users/edit-group/' . $group->id)]) }}

				<div class="form-group @if (isset($invalid_fields['name'])) has-error @endif">
					<label class="control-label" for="name">Name</label>

					{{ Form::text('name', Input::old('name', $group->name), [ 'class' => 'form-control' ]) }}

				    @if (isset($invalid_fields['name']))
				    	<span class="help-block">{{ $invalid_fields['name'] }}</span>
				    @endif
				</div>

				<div class="form-group @if (isset($invalid_fields['permissions'])) has-error @endif">
					<label class="control-label">Permissions</label>

					@include('coanda::admin.modules.users.includes.permissions', [ 'permissions' => $permissions, 'existing_permissions' => $existing_permissions ])

				    @if (isset($invalid_fields['permissions']))
				    	<span class="help-block">{{ $invalid_fields['permissions'] }}</span>
				    @endif
				</div>

				{{ Form::button('Update', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				{{ Form::button('Cancel', ['name' => 'cancel', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}

			{{ Form::close() }}

		</div>
	</div>
</div>

@stop
