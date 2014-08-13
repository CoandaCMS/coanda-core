<div class="main-navigation navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ Coanda::adminUrl('/') }}">Coanda CMS</a>
		</div>

		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">

				@set('menu_items', Coanda::adminMenu())
				@set('first_five', array_splice($menu_items, 0, 5))

				@foreach ($first_five as $menu_item)
					<li><a href="{{ Coanda::adminUrl($menu_item['url']) }}">{{ $menu_item['name'] }}</a></li>
				@endforeach

				{{-- Do we have any items left? --}}
				@if (count($menu_items) > 0)
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hidden-sm hidden-md hidden-lg">More options</span><span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							@foreach ($menu_items as $menu_item)
								<li><a href="{{ Coanda::adminUrl($menu_item['url']) }}">{{ $menu_item['name'] }}</a></li>
							@endforeach
						</ul>
					</li>
				@endif

			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ Coanda::currentUser()->first_name }} <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">Profile</a></li>
						<li><a href="#">Password</a></li>
						<li><a href="#">My Drafts</a></li>
						<li class="divider"></li>
						<li><a href="{{ Coanda::adminUrl('logout') }}">Log out</a></li>
					</ul>
				</li>
			</ul>

			<form class="navbar-form navbar-right" method="get" action="{{ Coanda::adminUrl('search') }}">
				<input type="text" class="form-control" placeholder="Search..." name="q">
			</form>	

			<div class="clearfix"></div>		
		</div>
      </div>
    </div>