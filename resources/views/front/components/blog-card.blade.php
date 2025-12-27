 @if($blog->status == 1)
    <div class="col-md-6 col-lg-4">
        <div class="blog-entry justify-content-end" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
            <a href="{{ route('public.blog.details', ['blog_slug' => $blog->slug]) }}" class="block-20 img"
                style="background-image: url('{{ $blog->getFirstMediaUrl('images') }}');">
            </a>
            <div class="text text-center">
                <p class="meta"><span><i class="fa fa-user me-1"></i>{{ $blog->datas['author'] ?? '' }}</span>
                    <span><i class="fa fa-calendar me-1"></i>{{ $blog->created_at->format('d M, Y') }}</span>
                    {{-- <span><a
                            href="#"><i class="fa fa-comment me-1"></i> 0 Comments</a></span> --}}
                            </p>
                <h3 class="heading mb-3"><a href="#">{{ $blog->name }}</a></h3>
                <p>{{ substr($blog->datas['description'] ?? '', 0, 100) }}...</p>
                <p><a href="{{ route('public.blog.details', ['blog_slug' => $blog->slug]) }}" class="btn btn-secondary">En
                        savoir plus <span class="ion-ios-arrow-round-forward me-2"></span></a></p>
            </div>
        </div>
    </div>
 @endif

