@extends('layouts.front')

@section('title', 'Bloq')

@section('content')
    <!-- Start Breadcrumb section -->
    <div class="breadcrumb-section"
        style="background-image:linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url({{ asset('front/assets/img/home1/about-banner.webp') }});">
        <div class="container">
            <div class="banner-content">
                <h1>Səyahət İlhamları</h1>
                <ul class="breadcrumb-list">
                    <li><a href="{{ route('front.home') }}">Ana səhifə</a></li>
                    <li>Bloq</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb section -->

    <!-- home4 blog Section Start-->
    <div class="home4-blog-section mb-100">
        <div class="container mt-50">
            <div class="row g-4 mb-40">
                <div class="col-lg-4 col-md-6 wow animate fadeInDown" data-wow-delay="200ms" data-wow-duration="1500ms">
                    <div class="blog-card2 two">
                        <div class="blog-img-wrap">
                            <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}" class="blog-img">
                                <img loading="lazy"
                                    src="https://images.rawpixel.com/image_800/cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDIzLTExL3N0YXJ0dXBpbWFnZXNfcmVhbF9waG90b19vZl9leG90aWNfaXNsYW5kX2xhbmRzY2FwZV9ub19vdGhlcl9pc181YmU4YWQ4OC1iZDMyLTQ5ZTAtODUyZi1lMWM1ZjhiMTljYTFfMS5qcGc.jpg"
                                    alt="">
                            </a>
                        </div>
                        <div class="blog-content">
                            <a href="travel-inspiration-01.html" class="blog-date"> 31 Yanvar 2025</a>
                            <h4><a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">
                                    Bir Günlük Turlar üçün Ən Yaxşı İstiqamətlər
                                </a></h4>
                            <p>
                                Qısa vaxtı olanlar üçün yaxın məsafəli, rahat və maraqlı marşrutlar.
                                Günlük turların üstünlükləri və kimlər üçün uyğun olduğu.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow animate fadeInDown" data-wow-delay="400ms" data-wow-duration="1500ms">
                    <div class="blog-card2 two">
                        <div class="blog-img-wrap">
                            <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}" class="blog-img">
                                <img loading="lazy"
                                    src="https://daily.hse.ru/storage/76942cd2-a712-43ef-bc82-ce3d7f8ab45f.jpg" alt="">
                            </a>
                        </div>
                        <div class="blog-content">
                            <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}" class="blog-date">23 Yanvar, 2025</a>
                            <h4><a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">
                                    Səyahətdən Əvvəl Bilməli Olduğunuz 7 Məsələ
                                </a></h4>
                            <p>
                                Sənədlər, geyim, büdcə planlaması, vaxt bölgüsü və səyahəti problemsiz
                                etmək üçün faydalı tövsiyələr.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow animate fadeInDown" data-wow-delay="600ms" data-wow-duration="1500ms">
                    <div class="blog-card2 two">
                        <div class="blog-img-wrap">
                            <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}" class="blog-img">
                                <img loading="lazy"
                                    src="https://www.ok-magazine.ru/images/cache/2022/6/28/q90_1470881_14e8d0a2c47a81ec7dd80df4a.jpeg"
                                    alt="">
                            </a>
                        </div>
                        <div class="blog-content">
                            <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}" class="blog-date">01 Aprel, 2025</a>
                            <h4><a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">
                                    Otel Seçərkən Edilən Ən Çox Səhvlər
                                </a></h4>
                            <p>
                                Reytinqlər, yerləşmə yeri, qiymət–keyfiyyət balansı və düzgün seçim üçün
                                real məsləhətlər.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- home4 blog Section End-->
@endsection