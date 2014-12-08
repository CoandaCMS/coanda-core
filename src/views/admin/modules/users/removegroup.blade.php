@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm remove user group')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('user') }}">Users</a></li>
			<li>Confirm deletion of user group</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Confirm deletion of '{{ $group->name }}'</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('users/remove-group/' . $group->id)]) }}
<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#group" data-toggle="tab">Group</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="group">

					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete the '{{ $group->name }}' user group
					</div>

					@set('user_count', $group->users()->count())
					<p>Please note, this will also detach {{ $user_count }} user{{ $user_count != 1 ? 's' : '' }}. Any users who are only in this group will be removed.</p>

                    {{ Form::button('I understand, please delete the group', ['type' => 'submit', 'class' => 'btn btn-danger']) }}
                    <a class="btn btn-default" href="{{ Coanda::adminUrl('users/group/' . $group->id) }}">Cancel</a>
				</div>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}
@stop
