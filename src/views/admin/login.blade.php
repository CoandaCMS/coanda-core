@extends('coanda::admin.layout.main')

@section('page_title', 'Log in')

@section('content')
<div class="row">

    <div class="login col-md-6 col-md-offset-3">

        <div class="login-header">
            <h4>Coanda CMS</h4>
        </div>

		@if ($errors->count() > 0)
			<div class="alert alert-danger">
				<p>Login failed, please try again.</p>
			</div>
		@endif

        {{ Form::open(array('url' => Coanda::adminUrl('login'), 'role' => 'form', 'class' => 'form-login' )) }}
                
			<div class="form-group no-margin @if ($errors->first('username')) has-error@endif">
				{{ Form::label('email', 'Email', array('class' => 'hidden')) }}
				{{ Form::text('email', Session::get('email'), array('class' => 'form-control input-lg', 'placeholder' => 'Email', 'autofocus')) }}

                @if($errors->first('username'))
                    <span class="help-block">{{ $errors->first('username') }}</span>
                @endif
			</div>

            <div class="form-group @if ($errors->first('password')) has-error@endif">
                {{ Form::label('password', 'Password', array('class' => 'hidden')) }}
                {{ Form::password('password', array('class' => 'form-control input-lg', 'placeholder' => 'Password')) }}

                @if($errors->first('password'))
                    <span class="help-block">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-block btn-lg btn-success">Sign In</button>
            </div>

        {{ Form::close() }}

    </div>

</div>
@stop
