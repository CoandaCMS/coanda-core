<nav class="main-navigation navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		<ul class="nav navbar-nav">
			<li><a href="{{ Coanda::adminUrl('/') }}">Dashboard</a></li>

			@foreach (Coanda::adminMenu() as $menu_item)
				<li><a href="{{ Coanda::adminUrl($menu_item['url']) }}">{{ $menu_item['name'] }}</a></li>
			@endforeach
		</ul>

		<ul class="nav navbar-nav navbar-right">
			<li><a href="{{ Coanda::adminUrl('logout') }}"><img src="{{ Coanda::currentUser()->avatar }}" class="img-circle" width="20"> Log out</a></li>
		</ul>
	</div>
</nav>