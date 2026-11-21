@extends('layouts.front')

@section('title', 'Post')

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



    <!-- Inspiration Details Page Start-->
    <div class="inspiration-details-page pt-100 mb-100">
        <div class="container">
            <div class="row g-lg-4 gy-5 justify-content-between">
                <div class="col-xl-7 col-lg-8">
                    <div class="inspiration-details">
                        <h2> Bir Günlük Turlar üçün Ən Yaxşı İstiqamətlər.</h2>
                        <span class="line-break"></span>
                        <br>
                        Qısa vaxtı olan, lakin gündəlik rutinindən çıxıb yeni təəssüratlar qazanmaq istəyənlər üçün
                        yaxın məsafəli, rahat və maraqlı marşrutlar ideal seçimdir. Bu tip marşrutlar adətən şəhərə
                        və ya yaşadığınız əraziyə yaxın olur, uzun yol yorğunluğu yaratmır və gün ərzində maksimum
                        səmərə əldə etməyə imkan verir. Bir gün ərzində təbiət qoynunda dincəlmək, tarixi məkanları
                        kəşf etmək, yerli mətbəxin dadına baxmaq və fərqli mühitdə vaxt keçirmək mümkündür.
                        </br></br>

                        Günlük turların əsas üstünlüklərindən biri vaxt baxımından çevik olmasıdır. Xüsusilə iş
                        qrafiki sıx olanlar, ailə və şəxsi öhdəliklərinə görə uzunmüddətli səyahət planlaşdıra
                        bilməyənlər üçün bu turlar böyük rahatlıq yaradır. Səhər yola düşüb axşam geri qayıtmaqla
                        həm istirahət edir, həm də növbəti gün üçün əlavə planlara ehtiyac qalmır. Bundan əlavə,
                        günlük turlar adətən daha sərfəli olur, çünki gecələmə xərcləri və uzunməsafəli nəqliyyat
                        tələb etmir.
                        </p>
                        <span class="line-break"></span>
                        <span class="line-break"></span>
                        <div class="inspiration-image mb-50">
                            <img loading="lazy"
                                src="https://daily.hse.ru/storage/76942cd2-a712-43ef-bc82-ce3d7f8ab45f.jpg" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-sidebar-area">
                        <div class="single-widget mb-30">
                            <h5 class="widget-title">
                                <svg width="22" height="22" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M20.9688 11C20.9726 12.1773 20.9019 13.3538 20.7569 14.5221C20.1691 19.1327 18.5625 20.9688 18.5625 20.9688H1.03125V18.9062C14.1316 17.7671 11.6596 7.89809 10.4264 4.54609C10.1607 4.50672 9.89326 4.48091 9.625 4.46875C9.625 4.46875 4.46875 4.125 4.8125 8.25C4.8125 8.25 2.0625 7.5625 2.75 4.8125C3.4375 2.0625 6.1875 2.75 6.1875 2.75C6.1875 2.75 6.875 1.03125 9.28125 1.03125C11.6875 1.03125 12.7188 3.09375 12.7188 3.09375C14.4375 1.03125 20.9688 3.78125 20.9688 11Z" />
                                    <path
                                        d="M10.9981 20.9687C10.9981 20.9687 15.4669 18.5625 15.4669 14.4375C15.4669 10.3125 13.0606 11.3437 13.0606 11.3437C13.0606 11.3437 13.4044 9.625 11.6856 8.9375C9.96688 8.25 8.93563 9.625 8.93563 9.625C8.93563 9.625 5.84188 8.25 5.15437 11C4.46688 13.75 7.56063 13.75 7.56063 13.75C7.56063 13.75 7.25692 11.7483 9.27938 11.3437C10.9981 11 15.8106 13.75 10.9981 20.9687Z" />
                                    <path
                                        d="M20.9697 11C20.9736 12.1773 20.9028 13.3538 20.7579 14.5222C18.5635 8.9375 15.9196 5.35777 10.4273 4.54609C10.1617 4.50672 9.89423 4.48091 9.62596 4.46875C9.62596 4.46875 4.46971 4.125 4.81346 8.25C4.81346 8.25 2.06346 7.5625 2.75096 4.8125C3.43846 2.0625 6.18846 2.75 6.18846 2.75C6.18846 2.75 6.87596 1.03125 9.28221 1.03125C11.6885 1.03125 12.7197 3.09375 12.7197 3.09375C14.4385 1.03125 20.9697 3.78125 20.9697 11Z" />
                                </svg>
                                Son Paylaşımlar
                            </h5>
                            <div class="recent-post-widget mb-30">
                                <div class="recent-post-img">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}"><img loading="lazy"
                                            src="https://images.musement.com/cover/0150/11/thumb_14910305_cover_header.jpeg?w=1200&h=630&q=95&fit=crop"
                                            alt=""></a>
                                </div>
                                <div class="recent-post-content">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">02 Mart, 2025</a>
                                    <h6><a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">
                                            Təbiət həvəskarları üçün ən yaxşı dağ tətilləri.
                                        </a></h6>
                                </div>
                            </div>
                            <div class="recent-post-widget mb-30">
                                <div class="recent-post-img">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}"><img loading="lazy"
                                            src="https://s.yimg.com/ny/api/res/1.2/Nicb2UZJ6qjmnxRmdvlkMQ--/YXBwaWQ9aGlnaGxhbmRlcjt3PTEyNDI7aD04Mjg-/https://media.zenfs.com/en/the_independent_577/c456120f27ad681cfb0bee9d32b3533a"
                                            alt=""></a>
                                </div>
                                <div class="recent-post-content">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">23 Mart, 2025</a>
                                    <h6><a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">
                                            Mədəniyyətə görə ən gözəl yerləri kəşf edin.
                                        </a></h6>
                                </div>
                            </div>
                            <div class="recent-post-widget mb-30">
                                <div class="recent-post-img">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}"><img loading="lazy"
                                            src="https://avatars.mds.yandex.net/get-altay/13916077/2a00000194b1a94b2962f91c933d174ce1d8/orig"
                                            alt=""></a>
                                </div>
                                <div class="recent-post-content">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">02 Aprel, 2025</a>
                                    <h6><a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">
                                            Xəyal edin. Kəşf edin. Kəşf edin. Növbəti dəfə hara gedəcəksiniz?
                                        </a></h6>
                                </div>
                            </div>
                            <div class="recent-post-widget">
                                <div class="recent-post-img">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}"><img loading="lazy"
                                            src="https://i.tez-tour.travel/img/hotels/7933/43.jpg" alt=""></a>
                                </div>
                                <div class="recent-post-content">
                                    <a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">10 Aprel, 2025</a>
                                    <h6><a href="{{ route('front.blog.show', ['slug' => 'detail']) }}">
                                            Təbiət, Mədəniyyət və Həyəcan: İlhamverici Səyahət Hekayələri.
                                        </a></h6>
                                </div>
                            </div>
                        </div>
                        <div class="single-widget mb-40">
                            <h5 class="widget-title">
                                <svg width="22" height="22" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true" focusable="false">
                                    <path
                                        d="M20.9688 11C20.9726 12.1773 20.9019 13.3538 20.7569 14.5221C20.1691 19.1327 18.5625 20.9688 18.5625 20.9688H1.03125V18.9062C14.1316 17.7671 11.6596 7.89809 10.4264 4.54609C10.1607 4.50672 9.89326 4.48091 9.625 4.46875C9.625 4.46875 4.46875 4.125 4.8125 8.25C4.8125 8.25 2.0625 7.5625 2.75 4.8125C3.4375 2.0625 6.1875 2.75 6.1875 2.75C6.1875 2.75 6.875 1.03125 9.28125 1.03125C11.6875 1.03125 12.7188 3.09375 12.7188 3.09375C14.4375 1.03125 20.9688 3.78125 20.9688 11Z" />
                                    <path
                                        d="M10.9981 20.9687C10.9981 20.9687 15.4669 18.5625 15.4669 14.4375C15.4669 10.3125 13.0606 11.3437 13.0606 11.3437C13.0606 11.3437 13.4044 9.625 11.6856 8.9375C9.96688 8.25 8.93563 9.625 8.93563 9.625C8.93563 9.625 5.84188 8.25 5.15437 11C4.46688 13.75 7.56063 13.75 7.56063 13.75C7.56063 13.75 7.25692 11.7483 9.27938 11.3437C10.9981 11 15.8106 13.75 10.9981 20.9687Z" />
                                    <path
                                        d="M20.9697 11C20.9736 12.1773 20.9028 13.3538 20.7579 14.5222C18.5635 8.9375 15.9196 5.35777 10.4273 4.54609C10.1617 4.50672 9.89423 4.48091 9.62596 4.46875C9.62596 4.46875 4.46971 4.125 4.81346 8.25C4.81346 8.25 2.06346 7.5625 2.75096 4.8125C3.43846 2.0625 6.18846 2.75 6.18846 2.75C6.18846 2.75 6.87596 1.03125 9.28221 1.03125C11.6885 1.03125 12.7197 3.09375 12.7197 3.09375C14.4385 1.03125 20.9697 3.78125 20.9697 11Z" />
                                </svg>
                                Kateqoriyalar
                            </h5>

                            <ul class="category-list" aria-label="Blog kateqoriyaları">
                                <li>
                                    <a href="#">
                                        <span>Macəra turları</span>
                                        <span>(10)</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <span>Dəniz və istirahət</span>
                                        <span>(05)</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <span>Mədəniyyət və irs turları</span>
                                        <span>(02)</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <span>Ailə ilə istirahət</span>
                                        <span>(08)</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <span>Kruizlər və okean səyahətləri</span>
                                        <span>(04)</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <span>Balayı və romantik turlar</span>
                                        <span>(06)</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <span>Büdcə dostu turlar</span>
                                        <span>(12)</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>

                </div>


            </div>
        </div>
    </div>
    <!-- Inspiration Details Page End-->
@endsection