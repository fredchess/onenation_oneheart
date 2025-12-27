<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="{{ route('admins.home') }}"><img src="{{ asset('lovecare/images/logo.png') }}"
                            style="height: 60px" alt="One Nation One Heart" srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            @if(auth()->user()->roles->pluck('name')->contains('responsable'))
                <ul class="menu">
                    <li class="sidebar-item @if (Request::segment(2) == trim('orphanages')) active @endif">
                        <a href="{{ route('orphanages.edit', $orphanage) }}" class='sidebar-link'>
                            <i class="bi bi-house-fill"></i>
                            <span>Mon orphelinat</span>
                        </a>
                    </li>
                </ul>
                @else
                <ul class="menu">
                    <li class="sidebar-title">Général</li>

                    <li class="sidebar-item @if (Request::segment(2) == trim('')) active @endif ">
                        <a href="{{ route('admins.home') }}" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item @if (Request::segment(2) == trim('orphanages')) active @endif">
                        <a href="{{ route('orphanages.index') }}" class='sidebar-link'>
                            <i class="bi bi-house-fill"></i>
                            <span>Orphelinats</span>
                        </a>
                    </li>
                    <li class="sidebar-item @if (Request::segment(2) == trim('blogs')) active @endif">
                        <a href="{{ route('blogs.index') }}" class='sidebar-link'>
                            <i class="bi bi-clipboard-data"></i>
                            <span>Blogs/Actions</span>
                        </a>
                    </li>
                    <li class="sidebar-item @if (Request::segment(2) == trim('partners')) active @endif">
                        <a href="{{ route('partners.index') }}" class='sidebar-link'>
                            <i class="bi bi-building"></i>
                            <span>Partenaires</span>
                        </a>
                    </li>
                    {{-- <li class="sidebar-item">
                    <a href="{{route("admins.home")}}" class='sidebar-link'>
                        <i class="bi bi-tag-fill"></i>
                        <span>Tags</span>
                    </a>
                </li> --}}

                    {{-- <li class="sidebar-item">
                    <a href="{{route("admins.home")}}" class='sidebar-link'>
                        <i class="bi bi-briefcase-fill"></i>
                        <span>Partenaires</span>
                    </a>
                </li> --}}
                    <li class="sidebar-item  @if (Request::segment(2) == trim('cities')) active @endif">
                        <a href="{{ route('cities.index') }}" class='sidebar-link'>
                            <i class="bi bi-map-fill"></i>
                            <span>Villes</span>
                        </a>
                    </li>

                    <li class="sidebar-title">Administration</li>
                    <li class="sidebar-item  @if (Request::segment(2) == trim('donations')) active @endif">
                        <a href="{{ route('donations.index') }}" class='sidebar-link'>
                            <i class="bi bi-cash-stack"></i>
                            <span>Dons</span>
                        </a>
                    </li>

                    <li class="sidebar-item @if (Request::segment(2) == trim('users')) active @endif">
                        <a href="{{ route('users.index') }}" class='sidebar-link'>
                            <i class="bi bi-person-fill"></i>
                            <span>Utilisateurs</span>
                        </a>
                    </li>

                    <li class="sidebar-item @if (Request::segment(2) == trim('responsables')) active @endif">
                        <a href="{{ route('responsables.index') }}" class='sidebar-link'>
                            <i class="bi bi-person-fill"></i>
                            <span>Responsables Orphelinats</span>
                        </a>
                    </li>
                    {{-- <li class="sidebar-item has-sub">
                    <a href="{{route("admins.home")}}" class='sidebar-link'>
                        <i class="bi bi-person-fill"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item ">
                            <a href="ui-map-google-map.html">Google Map</a>
                        </li>
                        <li class="submenu-item ">
                            <a href="ui-map-jsvectormap.html">JS Vector Map</a>
                        </li>
                    </ul>
                </li> --}}
                </ul>
            @endrole
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>
