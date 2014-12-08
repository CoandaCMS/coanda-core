@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm remove user')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('user') }}">Users</a></li>
			<li>Confirm deletion of user</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Confirm deletion of '{{ $user->present()->name }}'</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('users/remove-user/' . $user->id)]) }}
<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#user" data-toggle="tab">User</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="user">

					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete the following user
					</div>

                    <table class="table table-striped">
                        <tr>
                            <td>
                                <img src="{{ $user->avatar }}" class="img-circle" width="30">
                                {{ $user->present()->name }}
                            </td>
                            <td>{{ $user->present()->email }}</td>
                            <td>
                                {{ $user->last_login !== NULL ? 'Last login: ' . $user->present()->last_login : '' }}
                            </td>
                        </tr>
                    </table>

                    {{ Form::button('I understand, please delete the account', ['type' => 'submit', 'class' => 'btn btn-danger']) }}
                    <a class="btn btn-default" href="{{ Coanda::adminUrl('users/user/' . $user->id) }}">Cancel</a>
				</div>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}
@stop
