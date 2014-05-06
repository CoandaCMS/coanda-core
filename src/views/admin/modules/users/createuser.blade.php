@extends('coanda::admin.layout.main')

@section('page_title', 'New user')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('users') }}">Users</a></li>
			<li><a href="{{ Coanda::adminUrl('users/group/' . $group->id) }}">{{ $group->name }}</a></li>
			<li>New user</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Create new user</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="edit-container">
			{{ Form::open(['url' => Coanda::adminUrl('users/create-user/' . $group->id)]) }}

				<div class="row">
					<div class="col-md-6">
						<div class="form-group @if (isset($invalid_fields['first_name'])) has-error @endif">
							<label class="control-label" for="first_name">First name</label>

							{{ Form::text('first_name', Input::old('first_name'), [ 'class' => 'form-control' ]) }}

						    @if (isset($invalid_fields['first_name']))
						    	<span class="help-block">{{ $invalid_fields['first_name'] }}</span>
						    @endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group @if (isset($invalid_fields['last_name'])) has-error @endif">
							<label class="control-label" for="last_name">Last name</label>

							{{ Form::text('last_name', Input::old('last_name'), [ 'class' => 'form-control' ]) }}

						    @if (isset($invalid_fields['last_name']))
						    	<span class="help-block">{{ $invalid_fields['last_name'] }}</span>
						    @endif
						</div>
					</div>
				</div>

				<div class="form-group @if (isset($invalid_fields['email'])) has-error @endif">
					<label class="control-label" for="email">Email</label>

					{{ Form::text('email', Input::old('email'), [ 'class' => 'form-control' ]) }}

				    @if (isset($invalid_fields['email']))
				    	<span class="help-block">{{ $invalid_fields['email'] }}</span>
				    @endif
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group @if (isset($invalid_fields['password'])) has-error @endif">
							<label class="control-label" for="password">Password</label>

							{{ Form::password('password', [ 'class' => 'form-control' ]) }}

						    @if (isset($invalid_fields['password']))
						    	<span class="help-block">{{ $invalid_fields['password'] }}</span>
						    @endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group @if (isset($invalid_fields['password'])) has-error @endif">
							<label class="control-label" for="password_confirmation">Password confirmation</label>

							{{ Form::password('password_confirmation', [ 'class' => 'form-control' ]) }}
						</div>
					</div>
				</div>

				{{ Form::button('Create', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				{{ Form::button('Cancel', ['name' => 'cancel', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}

			{{ Form::close() }}
		</div>
	</div>
</div>

@stop
