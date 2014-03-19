<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	<div class="container">
		<ul class="nav navbar-nav">
			<li><a href="{{ Coanda::adminUrl('/') }}">Dashboard</a></li>
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
		</ul>

		<ul class="nav navbar-nav navbar-right">
			<li><a href="{{ Coanda::adminUrl('logout') }}"><span class="glyphicon glyphicon-user"></span> Log out</a></li>
		</ul>
	</div>
</nav>