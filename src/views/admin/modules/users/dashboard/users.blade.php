@set('online_users', Coanda::users()->currentlyOnline())
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="pull-right">Total users <span class="badge badge-default">{{ Coanda::users()->userCount() }}</span></span>
        Users
    </div>
    <div class="panel-body">
        @if ($online_users->count() > 0)
            @foreach ($online_users as $online_user)
                <p>
                    <span class="label label-info pull-right" class="font-size: 1.2em;">{{ $online_user->last_seen->diffForHumans() }}</span>
                    <img src="{{ $online_user->avatar }}" class="img-circle" width="30">
                    <a href="{{ Coanda::adminUrl('users/user/' . $online_user->id) }}">{{ $online_user->present()->name }}</a>
                </p>
            @endforeach
        @else
            <p>No users are online right now.</p>
        @endif
    </div>
</div>
