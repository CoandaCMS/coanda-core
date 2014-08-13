@extends('coanda::admin.layout.main')

@section('page_title', 'Profile')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('users') }}">Users</a></li>
			<li>Profile</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Your profile</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<a href="{{ Coanda::adminUrl('users/edit-profile') }}" class="btn btn-primary">Edit</a>
	</div>
</div>

<div class="row">
	<div class="col-md-8">

        <div class="page-tabs">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#profile" data-toggle="tab">Details</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="profile">

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
            </div>
        </div>

	</div>
</div>

@stop
