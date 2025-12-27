@extends('front.layouts.app')
@section('title', 'Contact')

@section('content')


    <section class="hero-wrap hero-wrap-2" style="background-image: url('{{asset("lovecare/images/bg_5.jpg")}}');">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-center">
          <div class="col-md-9 text-center">
            <p class="breadcrumbs"><span class="me-2"><a href="{{route("public.home")}}">Accueil <i class="fa fa-chevron-right"></i></a></span> <span>Contact <i class="fa fa-chevron-right"></i></span></p>
            <h1 class="mb-0 bread">Contact</h1>
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
                <h3>Contactez-nous</h3>
                <p class="mb-4">Nous sommes ouverts ! Laissez-nous un message et nous reviendrons vers vous très rapidement</p>
                <div class="row mb-4">
                  <div class="col-md-4">
                    <div class="dbox w-100 d-flex align-items-start">
                      <div class="text">
                        <p><span>Adresse:</span> Omnisports, Yaoundé, Cameroun</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="dbox w-100 d-flex align-items-start">
                      <div class="text">
                        <p><span>Email:</span> <a href="mailto:info@yoursite.com">contact@onoh.org</a></p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="dbox w-100 d-flex align-items-start">
                      <div class="text">
                        <p><span>Telephone / Whatsapp:</span> <span class="text">+237 6 55 02 98 67 / +33 6 14 59 75 93</span></p>
                      </div>
                    </div>
                  </div>
                </div>
                <form id="contactForm" name="contactForm" class="contactForm">
                  <div class="row">
                    <div class="col-lg-4">
                      <div class="form-group">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Nom complet">
                      </div>
                    </div>
                    <div class="col-lg-4"> 
                      <div class="form-group">
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Objet">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea name="message" class="form-control" id="message" cols="30" rows="7" placeholder="Votre message ici"></textarea>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <input type="submit" value="Envoyer le message" class="btn btn-primary">
                        <div class="submitting"></div>
                      </div>
                    </div>
                  </div>
                </form>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </section> 
    

@endsection
