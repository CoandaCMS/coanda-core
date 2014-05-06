@extends('coanda::admin.layout.main')

@section('page_title', 'User: ' . $user->present()->name)

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
		<h1 class="pull-left">{{ $user->present()->name }}</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		@if (Coanda::canView('users', 'edit'))
			<a href="{{ Coanda::adminUrl('users/edit-user/' . $user->id) }}" class="btn btn-primary">Edit</a>
		@else
			<span class="btn btn-primary" disabled="disabled">Edit</span>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-md-8">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li {{ $selected_tab == '' || $selected_tab == 'details' ? 'class="active"' : '' }}><a href="#details" data-toggle="tab">Details</a></li>
				<li {{ $selected_tab == 'groups' ? 'class="active"' : '' }}><a href="#groups" data-toggle="tab">Groups</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane {{ $selected_tab == '' || $selected_tab == 'details' ? 'active' : '' }}" id="details">
					<table class="table table-striped">
						<tr>
							<th>First name</th>
							<td>{{ $user->first_name }}</td>
						</tr>
						<tr>
							<th>Last name</th>
							<td>{{ $user->last_name }}</td>
						</tr>
						<tr>
							<th>Email</th>
							<td>{{ $user->email }}</td>
						</tr>
					</table>
				</div>

				<div class="tab-pane {{ $selected_tab == 'groups' ? 'active' : '' }}" id="groups">
					<h2>Current groups</h2>

					<table class="table table-striped">
						@foreach ($user->groups as $group)
							<tr>
								<td>
									<i class="fa fa-users"></i>
									<a href="{{ Coanda::adminUrl('users/group/' . $group->id) }}">{{ $group->name }}</a>
								</td>
								<td class="tight">
									@if ($user->groups->count() > 1)
										<a href="{{ Coanda::adminUrl('users/remove-from-group/' . $user->id . '/' . $group->id) }}"><i class="fa fa-minus-square-o"></i></a>
									@endif
								</td>
							</tr>
						@endforeach
					</table>

					@if ($user->unassigned_groups->count() > 0)
						<h2>Available groups</h2>

						<table class="table table-striped">
							@foreach ($user->unassigned_groups as $unassigned_group)
								<tr>
									<td>
										<i class="fa fa-users"></i>
										<a href="{{ Coanda::adminUrl('users/group/' . $unassigned_group->id) }}">{{ $unassigned_group->name }}</a>
									</td>
									<td class="tight">
										@if (Coanda::canView('users', 'edit'))
											<a href="{{ Coanda::adminUrl('users/add-to-group/' . $user->id . '/' . $unassigned_group->id) }}"><i class="fa fa-plus-square-o"></i></a>
										@else
											<span class="disabled"><i class="fa fa-plus-square-o"></i></span>
										@endif
									</td>
								</tr>
							@endforeach
						</table>
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
