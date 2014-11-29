<div class="main-navigation navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container-fluid">
		
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand @if (Coanda::adminLogo()) navbar-brand-logo @endif" href="{{ Coanda::adminUrl('/') }}">
				@if (Coanda::adminLogo()) 
					<img src="{{ Coanda::adminLogo() }}" alt="{{ Coanda::siteName() }}">
				@else
					{{ Coanda::siteName() }}
				@endif
				</a>
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
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> {{ Coanda::currentUser()->first_name }} <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="{{ Coanda::adminUrl('users/profile') }}">My Profile</a></li>
						<li class="divider"></li>
						<li><a href="#" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-power-off"></i> Log out</a></li>
					</ul>
				</li>
			</ul>

            @if (Coanda::canViewModule('pages'))
                <form class="navbar-form navbar-right" method="get" action="{{ Coanda::adminUrl('search') }}">
                    <input type="text" class="form-control" placeholder="Search..." name="q">
                </form>
            @endif

			<div class="clearfix"></div>		
		</div>
	</div>
</div>

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">×</span>
					<span class="sr-only">Close</span>
				</button>
				<p class="modal-title">Are you sure?</p>
			</div>
			<div class="modal-body">
				<a href="{{ Coanda::adminUrl('logout') }}" class="btn btn-primary">Yes, please log me out</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>