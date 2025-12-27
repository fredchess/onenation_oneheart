@extends('front.layouts.app')

@section('title', $blog->name)

@section('meta_title', $blog->name)
@section('meta_description', $blog->datas['description'] ?? '')
@section('meta_url', route('public.blog.details', ['blog_slug' => $blog->slug]))
@section('meta_image', $blog->getFirstMediaUrl('images'))

@section('content')

    <section class="hero-wrap hero-wrap-2" style="background-image: url('{{ $blog->getFirstMediaUrl('images') }}');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 text-center">
                    <p class="breadcrumbs"><span class="me-2"><a href="{{ route('public.home') }}">Accueil <i
                                    class="fa fa-chevron-right"></i></a></span> <span class="me-2"><a
                                href="{{ route('public.blog') }}">Blogs <i class="fa fa-chevron-right"></i></a></span>
                    </p>
                    <h1 class="mb-0 bread">{{ $blog->name }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row g-lg-5">
                <div class="col-lg-12">
                    <div class="content">
                        <?= $blog->datas['public_content'] ?? '' ?>
                    </div>

                    <div class="about-author d-flex p-4 bg-light rounded">
                        {{-- <div class="bio me-md-4 img" style="background-image: url(images/person_1.jpg);"></div> --}}
                        <div class="desc">
                            <h3>{{ $blog->datas['author'] ?? '' }}</h3>
                            <p>{{ $blog->datas['description'] ?? '' }}</p>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-4 col-md-12 mb-4 mb-lg-0 d-flex" style="flex-wrap: wrap">
                            @foreach ($blog->getMedia('images') as $media)
                                <img src="{{ $media->getFullUrl() }}" class="m-4 shadow-1-strong rounded"
                                    style="max-height: 200px" alt="" />
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-5 mt-5 d-none">
                        <h3 class="mb-5" style="font-size: 34px;">6 Comments</h3>
                        <ul class="comment-list">
                            <li class="comment">
                                <div class="vcard bio">
                                    <img src="images/person_1.jpg" alt="Image placeholder">
                                </div>
                                <div class="comment-body">
                                    <h3>John Doe</h3>
                                    <div class="meta">February 24, 2021 at 7:20am</div>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur quidem laborum
                                        necessitatibus, ipsam impedit vitae autem, eum officia, fugiat saepe enim sapiente
                                        iste iure! Quam voluptas earum impedit necessitatibus, nihil?</p>
                                    <p><a href="#" class="reply">Reply</a></p>
                                </div>
                            </li>

                            <li class="comment">
                                <div class="vcard bio">
                                    <img src="images/person_1.jpg" alt="Image placeholder">
                                </div>
                                <div class="comment-body">
                                    <h3>John Doe</h3>
                                    <div class="meta">February 24, 2021 at 7:20am</div>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur quidem laborum
                                        necessitatibus, ipsam impedit vitae autem, eum officia, fugiat saepe enim sapiente
                                        iste iure! Quam voluptas earum impedit necessitatibus, nihil?</p>
                                    <p><a href="#" class="reply">Reply</a></p>
                                </div>

                                <ul class="children">
                                    <li class="comment">
                                        <div class="vcard bio">
                                            <img src="images/person_1.jpg" alt="Image placeholder">
                                        </div>
                                        <div class="comment-body">
                                            <h3>John Doe</h3>
                                            <div class="meta">February 24, 2021 at 7:20am</div>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur quidem
                                                laborum necessitatibus, ipsam impedit vitae autem, eum officia, fugiat saepe
                                                enim sapiente iste iure! Quam voluptas earum impedit necessitatibus, nihil?
                                            </p>
                                            <p><a href="#" class="reply">Reply</a></p>
                                        </div>


                                        <ul class="children">
                                            <li class="comment">
                                                <div class="vcard bio">
                                                    <img src="images/person_1.jpg" alt="Image placeholder">
                                                </div>
                                                <div class="comment-body">
                                                    <h3>John Doe</h3>
                                                    <div class="meta">February 24, 2021 at 7:20am</div>
                                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur
                                                        quidem laborum necessitatibus, ipsam impedit vitae autem, eum
                                                        officia, fugiat saepe enim sapiente iste iure! Quam voluptas earum
                                                        impedit necessitatibus, nihil?</p>
                                                    <p><a href="#" class="reply">Reply</a></p>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="comment">
                                <div class="vcard bio">
                                    <img src="images/person_1.jpg" alt="Image placeholder">
                                </div>
                                <div class="comment-body">
                                    <h3>John Doe</h3>
                                    <div class="meta">February 24, 2021 at 7:20am</div>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur quidem laborum
                                        necessitatibus, ipsam impedit vitae autem, eum officia, fugiat saepe enim sapiente
                                        iste iure! Quam voluptas earum impedit necessitatibus, nihil?</p>
                                    <p><a href="#" class="reply">Reply</a></p>
                                </div>
                            </li>
                        </ul>
                        <!-- END comment-list -->

                        <div class="comment-form-wrap pt-5">
                            <h3 class="mb-5" style="font-size: 34px;">Leave a comment</h3>
                            <form action="#" class="p-4 p-lg-5 comment-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name *</label>
                                            <input type="text" class="form-control" id="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email *</label>
                                            <input type="email" class="form-control" id="email">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="website">Website</label>
                                            <input type="url" class="form-control" id="website">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="message">Message</label>
                                            <textarea name="" id="message" cols="30" rows="10"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="submit" value="Post Comment" class="btn py-3 px-4 btn-secondary">
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div> <!-- .col-md-8 -->
                <div class="col-lg-4 sidebar pl-md-4 d-none">
                    {{-- <div class="sidebar-box bg-light rounded">
                        <form action="#" class="search-form">
                            <div class="form-group">
                                <span class="icon fa fa-search"></span>
                                <input type="text" class="form-control" placeholder="Search...">
                            </div>
                        </form>
                    </div> --}}
                    <div class="sidebar-box">
                        <h3>Informations complémentaires</h3>
                        <ul class="categories me-md-4">
                            <li>
                                <strong>Nombre d'enfants : </strong>
                                <span>{{ $blog->datas['total_children'] ?? 0 }}</span>
                            </li>

                            <li>
                                <strong>Email : </strong>
                                <span>{{ $blog->datas['email'] ?? 'contact@onoh.org' }}</span>
                            </li>

                            <li>
                                <strong>Tel : </strong>
                                <span>{{ $blog->datas['tel'] ?? '' }}</span>
                            </li>
                        </ul>
                        {{-- <ul class="categories me-md-4">
                            <li><a href="#"><span class="ion-ios-arrow-round-forward"></span>Food</a></li>
                            <li><a href="#"><span class="ion-ios-arrow-round-forward"></span>Education</a></li>
                            <li><a href="#"><span class="ion-ios-arrow-round-forward"></span>Shelter</a></li>
                            <li><a href="#"><span class="ion-ios-arrow-round-forward"></span>Medical Health</a></li>
                        </ul> --}}
                    </div>

                    {{-- <div class="sidebar-box d-none">
                        <h3>Recent Blog</h3>
                        <div class="block-21 mb-4 d-flex">
                            <a class="blog-img me-4" style="background-image: url(images/image_1.jpg);"></a>
                            <div class="text">
                                <h3 class="heading"><a href="#">Lend Your Hands to the People Who Need Our Help</a>
                                </h3>
                                <div class="meta">
                                    <div><a href="#"><span class="fa fa-calendar"></span> February 24, 2021</a></div>
                                    <div><a href="#"><span class="fa fa-user"></span> Admin</a></div>
                                    <div><a href="#"><span class="fa fa-comment"></span> 19</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="block-21 mb-4 d-flex">
                            <a class="blog-img me-4" style="background-image: url(images/image_2.jpg);"></a>
                            <div class="text">
                                <h3 class="heading"><a href="#">Lend Your Hands to the People Who Need Our Help</a>
                                </h3>
                                <div class="meta">
                                    <div><a href="#"><span class="fa fa-calendar"></span> February 24, 2021</a></div>
                                    <div><a href="#"><span class="fa fa-user"></span> Admin</a></div>
                                    <div><a href="#"><span class="fa fa-comment"></span> 19</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="block-21 mb-4 d-flex">
                            <a class="blog-img me-4" style="background-image: url(images/image_3.jpg);"></a>
                            <div class="text">
                                <h3 class="heading"><a href="#">Lend Your Hands to the People Who Need Our Help</a>
                                </h3>
                                <div class="meta">
                                    <div><a href="#"><span class="fa fa-calendar"></span> February 24, 2021</a></div>
                                    <div><a href="#"><span class="fa fa-user"></span> Admin</a></div>
                                    <div><a href="#"><span class="fa fa-comment"></span> 19</a></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-box d-none">
                        <h3>Tag Cloud</h3>
                        <div class="tagcloud">
                            <a href="#" class="tag-cloud-link">donate</a>
                            <a href="#" class="tag-cloud-link">save</a>
                            <a href="#" class="tag-cloud-link">food</a>
                            <a href="#" class="tag-cloud-link">help</a>
                            <a href="#" class="tag-cloud-link">lend</a>
                            <a href="#" class="tag-cloud-link">hand</a>
                            <a href="#" class="tag-cloud-link">education</a>
                            <a href="#" class="tag-cloud-link">clothes</a>
                            <a href="#" class="tag-cloud-link">environment</a>
                            <a href="#" class="tag-cloud-link">help</a>
                            <a href="#" class="tag-cloud-link">water</a>
                            <a href="#" class="tag-cloud-link">charity</a>
                        </div>
                    </div> --}}

                    {{-- <div class="sidebar-box">
                        <h3>Paragraph</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus itaque, autem necessitatibus
                            voluptate quod mollitia delectus aut, sunt placeat nam vero culpa sapiente consectetur
                            similique, inventore eos fugit cupiditate numquam!</p>
                    </div> --}}
                </div>

            </div>
        </div>
    </section> <!-- .section -->

@endsection
