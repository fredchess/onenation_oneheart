<nav class="navbar navbar-expand-lg  ftco-navbar-light">
    <div class="container-xl">
        <a class="navbar-brand align-items-center" href="{{route("public.home")}}">
            <img src="{{ asset('lovecare/images/logo.png') }}" alt="Logo One Nation One Heart"
                style="height: 50px; margin: 10px">
            {{-- Love<small>Care</small>
          <span>Charity Theme</span> --}}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="fa fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link @if(Request::routeIs("public.home")) active @endif" href="{{route("public.home")}}">Accueil</a></li>
                <li class="nav-item"><a class="nav-link @if(Request::segment(1) == 'orphelinats') active @endif " href="{{route("public.orphanages")}}">Orphelinats</a></li>
                <li class="nav-item"><a class="nav-link @if(Request::routeIs("public.about")) active @endif " href="{{route("public.about")}}">À propos de ONOH</a></li>
                <li class="nav-item"><a class="nav-link @if(Request::routeIs("public.joinus")) active @endif " href="{{route("public.joinus")}}">Nous rejoindre</a></li>
                <li class="nav-item"><a class="nav-link @if(Request::segment(1) == "blog") active @endif " href="{{route("public.blog")}}">Blogs</a></li>
                <li class="nav-item"><a class="nav-link @if(Request::routeIs("public.contact")) active @endif " href="{{route("public.contact")}}">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>
