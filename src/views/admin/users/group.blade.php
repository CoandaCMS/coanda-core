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
		<a href="{{ Coanda::adminUrl('users/edit-group/' . $group->id) }}" class="btn btn-default">Edit</a>
		<a href="{{ Coanda::adminUrl('users/create-user/' . $group->id) }}" class="btn btn-primary">New user</a>
	</div>
</div>

<div class="row">
	<div class="col-md-8">
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
										<i class="fa fa-user"></i>
										<a href="{{ Coanda::adminUrl('users/user/' . $user->id) }}">{{ $user->present()->name }}</a>
									</td>
									<td>{{ $user->present()->email }}</td>
									<td class="tight"><a href="{{ Coanda::adminUrl('users/edit-user/' . $user->id) }}"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
							@endforeach
						</table>
					@else
						<p>This group doesn't have any users yet!</p>
					@endif
				</div>
				<div class="tab-pane" id="permissions">

					@if ($group->present()->permissions == 'all')
						<p>This group has full permissions to access all aspects of the CMS.</p>
					@else
						@foreach ($group->present()->permissions as $module => $permissions)
							<p><strong>{{ $module }}</strong></p>
							<ul>
								@foreach ($permissions as $permission)
									<li>{{ $permission }}</li>
								@endforeach
							</ul>
						@endforeach
					@endif

				</div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#search" data-toggle="tab">Search</a></li>
				<li><a href="#other" data-toggle="tab">Other</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="search">
					<input type="text" class="form-control" placeholder="Search users">
				</div>

				<div class="tab-pane" id="other">
					Something else
				</div>
			</div>
		</div>
	</div>
</div>

@stop
