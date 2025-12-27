<div class="col-md-6 col-lg-4 d-flex">
    <div class="blog-entry justify-content-end" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
        <a href="{{$partner["url"]}}" class="block-20 img"
            style="background-image: url('{{$partner->getFirstMediaUrl('images')}}');">
        </a>
        <div class="text text-center">
            <p class="meta"><span>{{$partner["domain"]}}</span></p>
            <h3 class="heading mb-3"><a href="{{$partner["url"]}}">{{$partner["name"]}}</a></h3>
            <p>{!! $partner["description"] !!}</p>
            <p><a href="{{$partner["url"]}}" class="btn btn-secondary" target="_blank">Découvrir <span class="ion-ios-arrow-round-forward me-2"></span></a>
            </p>
        </div>
    </div>
</div>
