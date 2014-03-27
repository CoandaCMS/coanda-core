@extends('coanda::admin.layout.main')

@section('page_title', 'New user group')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('users') }}">Users</a></li>
			<li>New user group</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Create new user group</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

<div class="row">
	<div class="col-md-12">

		{{ Form::open(['url' => Coanda::adminUrl('users/create-group')]) }}

			<div class="form-group @if (isset($invalid_fields['name'])) has-error @endif">
				<label class="control-label" for="name">Name</label>

				{{ Form::text('name', Input::old('name'), [ 'class' => 'form-control' ]) }}

			    @if (isset($invalid_fields['name']))
			    	<span class="help-block">{{ $invalid_fields['name'] }}</span>
			    @endif
			</div>

			<div class="form-group @if (isset($invalid_fields['permissions'])) has-error @endif">
				<label class="control-label">Permissions</label>
				
				<div class="checkbox">
					<label>
						<input type="checkbox" name="permissions[*][]" value="*" {{ (isset(Input::old('permissions')['*']) && in_array('*', Input::old('permissions')['*'])) ? 'checked="checked"' : '' }}>
						Everything
					</label>
				</div>

				@foreach ($permissions as $module_key => $module)
					<div class="checkbox">
						<label>
							<input type="checkbox" name="permissions[{{ $module_key }}][]" value="*" {{ (isset(Input::old('permissions')[$module_key]) && in_array('*', Input::old('permissions')[$module_key])) ? 'checked="checked"' : '' }}>
							{{ $module['name'] }}
						</label>
					</div>

					@foreach ($module['views'] as $view)
						<label class="checkbox-inline">
							<input type="checkbox" name="permissions[{{ $module_key }}][]" value="{{ $view }}" {{ (isset(Input::old('permissions')[$module_key]) && in_array($view, Input::old('permissions')[$module_key])) ? 'checked="checked"' : '' }}> {{ $view }}
						</label>
					@endforeach

				@endforeach

			    @if (isset($invalid_fields['permissions']))
			    	<span class="help-block">{{ $invalid_fields['permissions'] }}</span>
			    @endif
			</div>

			{{ Form::button('Create', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
			{{ Form::button('Cancel', ['name' => 'cancel', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}

		{{ Form::close() }}

	</div>
</div>

@stop
