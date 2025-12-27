@extends('front.layouts.app')
@section('title', 'Rejoignez-nous')

@section('css')
    <style>
      label{
        margin-top: 1rem;
      }

      .checkbox-item{
        margin: 5px 10px;
        padding: 5px;
        border: 2px dashed #FF000000;
        cursor: pointer;
      }
      .checkbox-item:active{
        margin: 5px 10px;
        padding: 5px;
        border: 2px dashed #FF000055;
      }
      .checkbox-item label{
        margin-top: 0px;
      }

</style>
@endsection
@section('content')


    <section class="hero-wrap hero-wrap-2" style="background-image: url('{{ asset('lovecare/images/bg_5.jpg') }}');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 text-center">
                    <p class="breadcrumbs"><span class="me-2"><a href="{{ route('public.home') }}">Accueil <i
                                    class="fa fa-chevron-right"></i></a></span> <span>Rejoignez-nous <i
                                class="fa fa-chevron-right"></i></span></p>
                    <h1 class="mb-0 bread">Rejoignez-nous</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ">
        <div class="container-xl">
            <div class="wrapper">
                <div class="row g-0">

                    <div class="col-lg-12">
                        <div class="contact-wrap w-100 p-md-5 p-4">
                            <span class="subheading">N'hésitez pas à nous rejoindre et mettre votre pierre à l'édifice.</span>
                            <h1>Formulaire d'inscription</h1>
                            <p class="mb-4">Nous vous remercions d'avance de vous inscrire en tant que bénévole.
                                Que ce soit à temps plein ou partiel, nous sommes déjà ravis d'avoir vos compétences dans
                                nos équipes</p>

                                @include("components.successError")
                            <div class="row mb-4 mt-5">

                                <div class="col-md-4">
                                    <div class="dbox w-100 d-flex align-items-start">
                                        <div class="text">
                                            <p><span>Si vous avez des questions, contactez-nous au:</span><span class="text">+237 6 55 02 98 67 / +33 6 14 59 75 93</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form id="become-volunteer" name="become-volunteer" class="become-volunteer" action="{{route("public.joinus.register")}}"  method="POST">
                              @csrf
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Nom complet</label>
                                            <input type="text" autofocus class="form-control" name="name" id="name" value="{{ old('name') }}"
                                                placeholder="Nom complet">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="">Email</label>
                                            <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}"
                                                placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">N° de telephone</label>
                                            <input type="tel" class="form-control" name="tel" id="tel" value="{{ old('tel') }}"
                                                placeholder="N° de téléphone ">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Comment as-tu connu One Nation One Heart (ONOH)?</label>
                                            <select name="provenance" class="form-control" id="" value="{{ old('provenance') }}">
                                                <option value="bouche-oreille">Bouche à oreille</option>
                                                <option value="reseaux-sociaux">Réseaux sociaux</option>
                                                <option value="autres">Autres</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="">Dans quelle ville résides-tu ?</label>

                                            <input type="text" class="form-control" name="city" id="city" value="{{ old('city') }}"
                                                placeholder="Votre ville de résidence">
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">As-tu deja été investi précédemment dans une association a but non
                                                lucratif? Si oui, parle nous-en en quelques mots.</label>
                                            <textarea name="past_experience" class="form-control" id="message" cols="30" rows="7"
                                                placeholder="As-tu deja été investi précédemment dans une association a but non lucrative? Si oui, parle nous-en en quelques mots.">{{ old('past_experience') }}</textarea>
                                        </div>
                                    </div>


                                    <div class="col-lg-12">
                                        <div class="form-group ">
                                            <label for="">Quelles sont tes preferences en terme d'activites au sein de ONOH
                                                ?</label>
                                            <div class="d-flex flex-wrap">
                                                <div class="checkbox-item">
                                                    <label for="preference-dev-web">
                                                        <input type="checkbox" id="preference-dev-web" name="preferences[]"
                                                            value="developpement-web" id="">
                                                        Pôle Developpement Web
                                                    </label>
                                                </div>
                                                <div class="checkbox-item">
                                                    <label for="preference-prospection">
                                                        <input type="checkbox" id="preference-prospection"
                                                            name="preferences[]" value="prospection">
                                                            Pôle Prospection
                                                    </label>
                                                </div>
                                                <div class="checkbox-item">
                                                    <label for="preference-financement-relation-donateur">
                                                        <input type="checkbox" id="preference-financement-relation-donateur"
                                                            name="preferences[]" value="financement-relation-donateur">
                                                            Pôle Finacement & Relation donateurs
                                                    </label>
                                                </div>
                                                <div class="checkbox-item">
                                                    <label for="preference-communication">
                                                        <input type="checkbox" id="preference-communication"
                                                            name="preferences[]" value="communication">
                                                            Pôle Communication
                                                    </label>
                                                </div>
                                                <div class="checkbox-item">
                                                    <label for="preference-juridique">
                                                        <input type="checkbox" id="preference-juridique" name="preferences[]"
                                                            value="juridique">
                                                            Pôle Juridique
                                                    </label>
                                                </div>
                                                <div class="checkbox-item">
                                                    <label for="preference-partenaire">
                                                        <input type="checkbox" id="preference-partenaire" name="preferences[]"
                                                            value="partenaire">
                                                            Pôle Recherche de Partenariats
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-12 mt-5">
                                        <div class="form-group">
                                            <input type="submit" value="Devenir bénévole" class="btn p-3 px-4 btn-primary">
                                            <div class="submitting"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            {{-- <div class="w-100 social-media mt-5">
                                <h3>Suivez-nous</h3>
                                <p>
                                    <a href="https://facebook.com/">Facebook</a>
                                    <a href="https://twitter.com/">Twitter</a>
                                    <a href="https://instagram.com/">Instagram</a>
                                </p>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
