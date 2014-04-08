<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<ul class="nav navbar-nav">
			<li><a href="{{ Coanda::adminUrl('/') }}">Dashboard</a></li>
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li><a href="{{ Coanda::adminUrl('users') }}">Users</a></li>
		</ul>

		<ul class="nav navbar-nav navbar-right">
			<li><a href="{{ Coanda::adminUrl('logout') }}"><img src="{{ Coanda::currentUser()->avatar }}" class="img-circle" width="20"> Log out</a></li>
		</ul>
	</div>
</nav>