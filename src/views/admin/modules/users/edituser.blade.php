@extends('coanda::admin.layout.main')

@section('page_title', 'Edit user')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('users') }}">Users</a></li>
			<li>{{ $user->present()->name }}</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Edit {{ $user->present()->name }}</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

<div class="row">
	<div class="col-md-12">

		<div class="edit-container">

			{{ Form::open(['url' => Coanda::adminUrl('users/edit-user/' . $user->id)]) }}

				<div class="row">
					<div class="col-md-6">
						<div class="form-group @if (isset($invalid_fields['first_name'])) has-error @endif">
							<label class="control-label" for="first_name">First name</label>

							{{ Form::text('first_name', Input::old('first_name', $user->first_name), [ 'class' => 'form-control' ]) }}

						    @if (isset($invalid_fields['first_name']))
						    	<span class="help-block">{{ $invalid_fields['first_name'] }}</span>
						    @endif
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group @if (isset($invalid_fields['last_name'])) has-error @endif">
							<label class="control-label" for="last_name">Last name</label>

							{{ Form::text('last_name', Input::old('last_name', $user->last_name), [ 'class' => 'form-control' ]) }}

						    @if (isset($invalid_fields['last_name']))
						    	<span class="help-block">{{ $invalid_fields['last_name'] }}</span>
						    @endif
						</div>
					</div>
				</div>

				<div class="form-group @if (isset($invalid_fields['email'])) has-error @endif">
					<label class="control-label" for="email">Email</label>

					{{ Form::text('email', Input::old('email', $user->email), [ 'class' => 'form-control' ]) }}

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
						    @else
						    	<span class="help-block">If you do not wish to change the password, just leave these fields blank.</span>
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

				{{ Form::button('Update', ['name' => 'save', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				{{ Form::button('Cancel', ['name' => 'cancel', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}

			{{ Form::close() }}

		</div>

	</div>
</div>

@stop
