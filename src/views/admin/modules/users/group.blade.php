@extends('coanda::admin.layout.main')

@section('page_title', 'User group: ' . $group->name)

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
		<h1 class="pull-left">{{ $group->name }}</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Users {{ $group->users->count() }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		@if (Coanda::canView('users', 'edit'))
			<a href="{{ Coanda::adminUrl('users/edit-group/' . $group->id) }}" class="btn btn-default">Edit</a>
		@else
			<span class="btn btn-primary" disabled="disabled">Edit</span>
		@endif

		<div class="btn-group">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				More
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					@if (Coanda::canView('user', 'remove'))
						<a href="{{ Coanda::adminUrl('users/remove-group/' . $group->id) }}">Delete</a>
					@else
						<span class="disabled">Delete</span>
					@endif
				</li>
			</ul>
		</div>

		@if (Coanda::canView('users', 'create'))
			<a href="{{ Coanda::adminUrl('users/create-user/' . $group->id) }}" class="btn btn-primary">New user</a>
		@else
			<span class="btn btn-primary" disabled="disabled">New user</span>
		@endif

	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#users" data-toggle="tab">Users</a></li>
				<li><a href="#permissions" data-toggle="tab">Permissions</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="users">
					@if ($group->users->count() > 0)
						<table class="table table-striped">
							@foreach ($group->users as $user)
								<tr>
									<td>
										<img src="{{ $user->avatar }}" class="img-circle" width="20">
										<a href="{{ Coanda::adminUrl('users/user/' . $user->id) }}">{{ $user->present()->name }}</a>
									</td>
									<td>{{ $user->present()->email }}</td>
									<td>
										{{ $user->last_login !== NULL ? 'Last login: ' . $user->present()->last_login : '' }}
									</td>
									<td class="tight">
										@if (Coanda::canView('users', 'edit'))
											<a href="{{ Coanda::adminUrl('users/edit-user/' . $user->id) }}"><i class="fa fa-pencil-square-o"></i></a>
										@else
											<span class="disabled"><i class="fa fa-pencil-square-o"></i></span>
										@endif
									</td>
								</tr>
							@endforeach
						</table>
					@else
						<p>This group doesn't have any users yet!</p>
					@endif
				</div>
				<div class="tab-pane" id="permissions">
					@include('coanda::admin.modules.users.includes.permissionsview', [ 'permissions' => Coanda::availablePermissions(), 'existing_permissions' => $group->access_list ])
				</div>
			</div>
		</div>
	</div>
</div>

@stop
