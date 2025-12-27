@extends('front.layouts.app')

@section('title', 'Orphelinats')
@section('content')


    <section class="hero-wrap hero-wrap-2" style="background-image:  url('{{ asset('lovecare/images/bg_5.jpg') }}');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 text-center">
                    <p class="breadcrumbs"><span class="me-2"><a href="{{ route('public.home') }}">Accueil <i
                                    class="fa fa-chevron-right"></i></a></span> <span>Orphelinats <i
                                class="fa fa-chevron-right"></i></span></p>
                    <h1 class="mb-0 bread">Orphelinats</h1>

                </div>

            </div>

        </div>
    </section>

    <section class="ftco-causes ftco-section">
        <div class="container-xl">

            <div class="row">
                <div class="col-12">
                    <h5>Recherchez parmi les orphelinats</h5>
                    <div class="sidebar-box bg-light rounded">
                        <form action="{{ route('public.orphanages') }}" method="GET" class="search-form">
                            <div class="form-group">
                                <span class="icon fa fa-search"></span>
                                <input type="text" name="search" class="form-control" placeholder="Search...">
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-sm-12 mt-2 mb-2">
                                    <div class="form-group">
                                        <select name="villes[]" id="cities-select" class="form-control selectpicker-city" multiple data-live-search="true">
                                            <option value="" disabled>Selectionner une ville</option>
                                            @foreach ($villes as $ville)
                                                <option value="{{ $ville->name }}">{{ $ville->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-sm-12 mt-2 mb-2">
                                    <div class="form-group">
                                        <span class="icon fas fa-location-dot"></span>
                                        <input type="text" name="street" class="form-control" placeholder="Quartier">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-sm-12 mt-2 mb-2">
                                    <div class="form-group">
                                        <select name="ages[]" id="ages-select" class="form-control selectpicker-age" multiple data-live-search="true">
                                            <option value="" disabled>Selectionner une tranche d'age</option>
                                            <option value="1">0-6 ans</option>
                                            <option value="2">7-13 ans</option>
                                            <option value="3">14-21 ans</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="d-flex flex-end items-center">
                                    <strong class="mr-2">Trier par</strong> &nbsp;
                                    <select name="sort" id="sort-select">
                                        <option value="">---</option>
                                        <option value="1">Nombre d'enfants croissant</option>
                                        <option value="2">Nombre d'enfants décroissant</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Rechercher</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                @if($orphelinats->count()> 0)
                @foreach ($orphelinats as $orphelinat)
                    @include("front.components.orphanage-card", ["orphanage" => $orphelinat])
                @endforeach
                @else
                <div class="d-flex" style="flex-direction: column; align-items: center;">
                <img src="{{asset("lovecare/images/empty.svg")}}" style="margin: 50px auto; width: 300px" />
                Pas d'orphelinats trouvés
                </div>
                @endif
            </div>

            <div class="row mt-5">
                <div class="col text-center">
                    <div class="block-27">
                        {!! $orphelinats->links() !!}
                    </div>
                </div>
            </div>

        </div>
    </section>

    <style>
        /* .relative.z-0.inline-flex.shadow-sm.rounded-md { display: none } */
        .block-27, .block-27 a.page-link, .block-27 span.page-link {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .flex-end {
            justify-content: flex-end
        }

        .items-center { align-items: center }

        select{
            height: 50px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding-left: 5px;
            padding: 10px;
            font-size: 18px;
            font-family: 'Open Sans', sans-serif;
            color: #555;
            background-color: rgb(255, 255, 255);
            background-image: none;
            border: 1px solid rgb(41, 18, 18);
            outline: none
        }
        select>option{
            font-size: 18px;
            font-family: 'Open Sans', sans-serif;
            color: #555;
            background-color: rgb(247, 247, 247);
            background-image: none;
            font-size: 18px;
            height: 50px;
            padding: 15px;
            border: 1px solid rgb(41, 18, 18);
        }
    </style>
@endsection
