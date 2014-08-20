a,
a:hover,
a:focus,
.pagination > li > a,
.pagination > li > span,
.pagination > li > a:hover,
.pagination > li > span:hover,
.pagination > li > a:focus,
.pagination > li > span:focus
{
	color: {{ Config::get('coanda::coanda.admin_colour') }};
}

.pagination > .active > a,
.pagination > .active > span,
.pagination > .active > a:hover,
.pagination > .active > span:hover,
.pagination > .active > a:focus,
.pagination > .active > span:focus
{
	background: {{ Config::get('coanda::coanda.admin_colour') }};
}

.btn-primary
{
	background: {{ Config::get('coanda::coanda.admin_colour') }};
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary.active,
.open .dropdown-toggle.btn-primary
{
	background: {{ Config::get('coanda::coanda.admin_colour') }};
}

.breadcrumb-nav
{
	background: {{ Config::get('coanda::coanda.admin_colour') }};
}

tr.status-draft td a .fa
{
	color: {{ Config::get('coanda::coanda.admin_colour') }};
}

