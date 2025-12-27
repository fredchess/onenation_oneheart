 @if (isset($volunteer->datas['visible']) && $volunteer->datas['visible'] == 1)

     <div class="col-md-6 col-lg-3" data-aos="flip-right" data-aos-delay="100" data-aos-duration="1000">
         <div class="staff">
             <div class="img-wrap d-flex align-items-stretch">
                 <div class="img align-self-stretch"
                     style="background-image: url({{ $volunteer->getFirstMediaUrl() }});"></div>
             </div>
             <div class="text text-center">
                 <h3 class="mb-2">{{ $volunteer->name }}</h3>
                 <span class="position mb-2">{{ $volunteer->datas['title'] ?? 'Volunteer' }}</span>
                 <div class="faded">
                     <ul class="ftco-social text-center">
                         @if ($volunteer->datas != null)
                             @if (isset($volunteer->datas['facebook']))
                                 <li class="ftco-animate"><a href="{{ $volunteer->datas['facebook'] }}"
                                         class="d-flex align-items-center justify-content-center"><span
                                             class="fa fa-facebook"></span></a></li>
                             @endif
                             @if (isset($volunteer->datas['twitter']))
                                 <li class="ftco-animate"><a href="{{ $volunteer->datas['twitter'] }}"
                                         class="d-flex align-items-center justify-content-center"><span
                                             class="fa fa-twitter"></span></a></li>
                             @endif
                             @if (isset($volunteer->datas['instagram']))
                                 <li class="ftco-animate"><a href="{{ $volunteer->datas['instagram'] }}"
                                         class="d-flex align-items-center justify-content-center"><span
                                             class="fa fa-instagram"></span></a></li>
                             @endif
                             @if (isset($volunteer->datas['linkedin']))
                                 <li class="ftco-animate"><a href="{{ $volunteer->datas['linkedin'] }}"
                                         class="d-flex align-items-center justify-content-center"><span
                                             class="fa fa-linkedin"></span></a></li>
                             @endif
                         @endif
                     </ul>
                     <p>{{ $user->datas['description'] ?? '' }}</p>
                 </div>
             </div>
         </div>
     </div>

 @endif
