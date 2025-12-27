@extends('front.layouts.app')

@section('content')

    <section class="hero-wrap rellax" data-rellax-speed="-5" data-rellax-min="-120" data-rellax-max="0"
        style="background-image: url('{{ asset('lovecare/images/bg_1.jpg') }}');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-center">
                <div class="col-lg-6">
                    <span class="subheading">One Nation One Heart</span>
                    <h1 class="mb-4">Smile Together</h1>
                    <p><a href="#donate" class="btn btn-primary p-4 py-3">Faire un don maintenant <span
                                class="ion-ios-arrow-round-forward"></span></a>
                        {{-- <a href="#" class="btn">Watch the
                            Video <span class="ion-ios-play"></span></a> --}}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <div class="">
        @if (request()->input('success') == 'true')
            <div class="position-fixed info-donation py-2 px-5 m-auto mt-1 text-center"
                style="width: max-content; background-color: #3d8601; color: #fff">Payment effectué avec succes</div>
        @endif
        @if (request()->input('success') == 'false')
            <div class="position-fixed info-donation py-2 px-5 m-auto mt-1 text-center"
                style="width: max-content; background-color: #db4d65; color: #fff">Le payment n'a pas été effectué</div>
        @endif
        @if (session('error'))
            <div class="position-fixed info-donation py-2 px-5 m-auto mt-1 text-center"
                style="width: max-content; background-color: #db4d65; color: #fff">{{ session('error') }}</div>
        @endif
    </div>

    <section class="ftco-intro-wrap" id="donate">
        <div class="container-xl">
            <div class="row g-lg-5">
                <div class="col-md-5 order-lg-last d-flex align-items-stretch">
                    <div class="fund-wrap">
                        @include('components.donation-form')
                    </div>
                </div>
                <div class="col-md-7 heading-section d-flex align-items-center" data-aos="fade-up" data-aos-delay="200"
                    data-aos-duration="1000">
                    <div class="mt-0 about-wrap">
                        <span class="subheading">Bienvenue chez One Nation, One Heart</span>
                        <h2 class="mb-4">Pour une action sociale, humanitaire et solidaire,</h2>
                        <p>Sur cette plateforme, nous recensons les orphelinats et centres sociaux légaux sur
                            toute l’étendue du territoire national Camerounais.
                        </p>
                        <p>
                            Ceux-ci sont répartis suivant divers critères vous permettant de les découvrir aisément:
                        <ul>
                            <li>La localisation : la région, le département, la ville et le quartier</li>
                            <li>Leur capacité d'accueil : le nombre d'enfants pris en charge dans chaque structure, la
                                tranche
                                d'âge de ces enfants
                            </li>
                            <li>Leurs besoins : financiers, sanitaires, denrées alimentaires, vestimentaires, etc...
                            </li>
                            <li>Leur histoire et les contacts des responsables de ces diverses structures.</li>
                        </ul>
                        </p>
                        <p>
                            L'idée étant de casser les codes préétablis et préjugés de l’action sociale au Cameroun et
                            rendre chacun acteur du bonheur d’autrui.</p>

                        <div class="row mt-5 g-md-3">
                            <div class="col-md-6 col-lg-4 mb-2 mb-md-0 d-flex align-items-stretch">
                                <a href="{{ route('public.contact') }}?add_orphanage" class="services-2">
                                    <div class="icon"><span class="flaticon-donation"></span></div>
                                    <div class="text">
                                        <h2>Proposer un orphelinat</h2>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2 mb-md-0 d-flex align-items-stretch">
                                <a href="mailto:contact@onoh.org?subject=Devenir partenaire" class="services-2 color-2">
                                    <div class="icon"><span class="flaticon-ecosystem"></span></div>
                                    <div class="text">
                                        <h2>Devenir partenaire</h2>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2 mb-md-0 d-flex align-items-stretch">
                                <a href="{{ route('public.joinus') }}" class="services-2 color-3">
                                    <div class="icon"><span class="flaticon-charity"></span></div>
                                    <div class="text">
                                        <h2>Devenir bénévole</h2>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section-counter img" style="background-image: url({{ asset('lovecare/images/bg_3.jpg') }});">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 heading-section heading-section-white mb-5" data-aos="fade-up"
                    data-aos-duration="1000">
                    <span class="subheading">Quelques chiffres de ONOH</span>
                    <h2 class="mb-0">Chiffres clés</h2>
                </div>
            </div>
            <div class="row section-counter">
                <div class="col-sm-6 03col-md-6 col-lg-3 d-flex align-items-stretch">
                    <div class="counter-wrap-2" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                        <div class="icon">
                            <span class="flaticon-heart"></span>
                        </div>
                        <h2 class="number"><span class="countup">{{ $total_orphanages }}</span>
                        </h2>
                        <span class="caption">Orphelinats enregistrés</span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3 d-flex align-items-stretch">
                    <div class="counter-wrap-2" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1000">
                        <div class="icon">
                            <span class="icon fa fa-map marker"></span>
                        </div>
                        <h2 class="number"><span class="countup">{{ $total_cities }}</span></h2>
                        <span class="caption">Régions couvertes</span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3 d-flex align-items-stretch">
                    <div class="counter-wrap-2" data-aos="fade-up" data-aos-delay="300" data-aos-duration="1000">
                        <div class="icon">
                            <span class="flaticon-donation"></span>
                        </div>
                        <h2 class="number"><span class="countup">{{ $total_enfants }}</span></h2>
                        <span class="caption">enfants recensés</span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3 d-flex align-items-stretch">
                    <div class="counter-wrap-2" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                        <div class="icon">
                            <span class="flaticon-ecosystem"></span>
                        </div>
                        <h2 class="number"><span class="countup">3</span></h2>
                        <span class="caption">Partenaires </span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3 d-flex align-items-stretch mt-5">
                    <div class="counter-wrap-2" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                        <div class="icon">
                            <span class="flaticon-charity"></span>
                        </div>
                        <h2 class="number"><span class="countup">33</span></h2>
                        <span class="caption">bénévoles</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-causes ftco-section ">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div class="col-lg-5 heading-section text-center mb-4" data-aos="fade-up" data-aos-duration="1000">
                    <span class="subheading">Nos actions menées grâce à vous </span>
                    <h2 class="mb-4">Actions menées</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    @if ($blogs->count() > 0)
                        <div class="carousel-causes">
                            @foreach ($blogs as $blog)
                                @include('front.components.blog-card', ['blog' => $blog])
                            @endforeach
                        </div>
                    @else
                        <div class="d-flex"
                            style="flex-direction: column; align-items: center; justify-content:center; margin: auto;">
                            <img src="{{ asset('lovecare/images/empty.svg') }}"
                                style="margin: 50px auto; width: 300px" />
                            Pas d'articles enregistré pour l'instant
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-image img" style="background-image: url({{ asset('lovecare/images/bg_2.jpg') }});">
        <div class="overlay"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 heading-section heading-section-white mb-5 text-center" data-aos="fade-up"
                    data-aos-duration="1000">
                    <span class="subheading">Faites un don ou devenez volontaires</span>
                    <h2 class="mb-4">Le plus petit acte de bonté vaut plus que la plus grande intention</h2>
                    <p><a href="#" class="btn btn-primary py-3 px-4">Faire un don maintenant !</a> <a> </a><a
                            href="{{ route('public.joinus') }}" class="btn btn-secondary py-3 px-4">Devenir bénévole</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container-xl">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-7 heading-section text-center" data-aos="fade-up" data-aos-duration="1000">
                    <span class="subheading">Partenaires</span>
                    <h2>Découvrez nos partenaires</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                {{-- La Mater Market --}}
                @foreach ($partners as $partner)
                    @include('front.components.partner-card', ['partner' => $partner])
                @endforeach
            </div>
        </div>
    </section>
    <section class="ftco-section testimony-section img"
        style="background-image: url({{ asset('lovecare/images/bg_4.jpg') }});">
        <div class="overlay"></div>
        <div class="container-xl">
            <div class="row justify-content-center pb-4">
                <div class="col-lg-7 text-center heading-section heading-section-white" data-aos="fade-up"
                    data-aos-duration="1000">
                    <span class="subheading">Témoignages</span>
                    <h2 class="mb-5">Ce qu'ils disent de nous</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="carousel-testimony">

                        @foreach ($testimonies as $testimony)
                            @include('front.components.testimony-item', ['testimony' => $testimony])
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ftco-no-pt ftco-no-pb">
        <div class="container-xl">
            <div class="row g-lg-5">
                <div class="col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100"
                    data-aos-duration="1000">
                    <div class="img img-2 w-100"
                        style="background-image: url({{ asset('lovecare/images/about.jpg') }});">
                    </div>
                </div>
                <div class="col-md-6 heading-section d-flex align-items-center" data-aos="fade-up" data-aos-delay="200"
                    data-aos-duration="1000">
                    <div class="mt-0 my-lg-5 py-5">
                        <span class="subheading">Apportez un soutien humain à l'aventure ONOH</span>
                        <h2 class="mb-4">Appel à bénévoles</h2>
                        <p>Couvrir l’étendue du territoire afin d’avoir une proximité avec la quasi-totalité des
                            orphelinats
                            et centres sociaux. Faciliter les descentes et faire connaître l’association.
                        </p>
                        <p>N'hésitez pas à nous rejoindre et mettre votre pièrre à l'édifice.
                        </p>
                        <p><a href="{{ route('public.joinus') }}" class="btn btn-secondary py-3 px-4">Devenir
                                bénévole</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            const id = setTimeout(() => {
                document.querySelector('.info-donation').classList.add('hidden')
            }, 2000);
        })
    </script>

@endsection

@section('css')
    <style>
        .info-donation.position-fixed {
            top: 20px;
            left: 50%;
            z-index: 1000;
            transform: translateX(-50%);
            opacity: 1;
            transition: all 1s;
        }

        .info-donation.hidden {
            transform: translate(-50%, -20px);
            opacity: 0;
        }
    </style>
@endsection
