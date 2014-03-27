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
				
				<div class="checkbox">
					<label>
						<input type="checkbox" name="permissions[*][]" value="*" {{ (in_array('*', $existing_permissions) || (isset($existing_permissions['*']) && in_array('*', $existing_permissions['*']))) ? 'checked="checked"' : '' }}>
						Everything
					</label>
				</div>

				@foreach ($permissions as $module_key => $module)
					<div class="checkbox">
						<label>
							<input type="checkbox" name="permissions[{{ $module_key }}][]" value="*" {{ (isset($existing_permissions[$module_key]) && in_array('*', $existing_permissions[$module_key])) ? 'checked="checked"' : '' }}>
							{{ $module['name'] }}
						</label>
					</div>

					@foreach ($module['views'] as $view)
						<label class="checkbox-inline">
							<input type="checkbox" name="permissions[{{ $module_key }}][]" value="{{ $view }}" {{ (isset($existing_permissions[$module_key]) && in_array($view, $existing_permissions[$module_key])) ? 'checked="checked"' : '' }}> {{ $view }}
						</label>
					@endforeach

				@endforeach

			    @if (isset($invalid_fields['permissions']))
			    	<span class="help-block">{{ $invalid_fields['permissions'] }}</span>
			    @endif
			</div>

			{{ Form::button('Update', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
			{{ Form::button('Cancel', ['name' => 'cancel', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}

		{{ Form::close() }}

	</div>
</div>

@stop
