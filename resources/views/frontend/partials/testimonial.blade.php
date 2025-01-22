@php
    $testimonial = getSectionData('testimonial-section');
    $defualt = get_default_language_code()??'en';
@endphp

<section class="testimonial-section pt-120">
    {{-- <img src="{{ asset('public/frontend/') }}/images/client/client-shape-1.svg" alt="shape" class="testimonial-shape-one">
    <img src="{{ asset('public/frontend/') }}/images/client/client-shape-2.svg" alt="shape" class="testimonial-shape-two"> --}}
    {{-- <div class="mask-shape">
        <img src="{{ asset('public/frontend/') }}/images/client/client-shape-3.jpg" style="-webkit-mask-box-image: url('{{ asset('public/frontend/') }}/images/client/client-shape-5.svg'); mask: url('assets/images/client/client-shape-5.svg');" alt="mask-shape" class="testimonial-shape-three">
    </div> --}}
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-7 text-center">
                <div class="section-header">
                    <span class="section-sub-title">{{ __(@$testimonial->value->language->$defualt->title ?? @$testimonial->value->language->$default_lng->title) }}</span>
                        @php
                            $header = explode('|', @$testimonial->value->language->$defualt->heading ?? @$testimonial->value->language->$default_lng->heading );
                        @endphp
                      @if(isset($header) ||   $header != null)
                      <h2 class="section-title">@isset($header[0]) {{ $header[0] }} @endisset <span>@isset($header[1]) {{ $header[1] }} @endisset</span></h2>
                      @else
                      <h2 class="section-title">What People Say About <span>Our Foundation</span></h2>
                      @endif

                </div>
            </div>
        </div>
        <div class="testimonial-area">
            {{-- <ul class="testimonial-box-image">
                <li>
                    <img src="{{ get_image(@$testimonial->value->images->small_image_one,'site-section') }}" alt="client">
                </li>
                <li>
                    <img src="{{ get_image(@$testimonial->value->images->small_image_two,'site-section') }}" alt="client">
                </li>
                <li>
                    <img src="{{ get_image(@$testimonial->value->images->small_image_three,'site-section') }}" alt="client">
                </li>
                <li>
                    <img src="{{ get_image(@$testimonial->value->images->small_image_four,'site-section') }}" alt="client">
                </li>
            </ul> --}}
            <div class="testimonial-slider">
                <div class="swiper-wrapper">
                    @if(isset($testimonial->value->items))
                    @foreach($testimonial->value->items ?? [] as $key => $item)
                    <div class="swiper-slide">
                        <div class="testimonial-wrapper">
                            <div class="thumb">
                                <img src="{{ get_image(@$item->image,'site-section') }}" alt="client">
                            </div>
                            <div class="content">
                                <div class="client-quote">
                                    <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.75 10.9951H18V7.99512C18 6.35449 19.3125 4.99512 21 4.99512H21.375C21.9844 4.99512 22.5 4.52637 22.5 3.87012V1.62012C22.5 1.01074 21.9844 0.495117 21.375 0.495117H21C16.8281 0.495117 13.5 3.87012 13.5 7.99512V19.2451C13.5 20.5107 14.4844 21.4951 15.75 21.4951H21.75C22.9688 21.4951 24 20.5107 24 19.2451V13.2451C24 12.0264 22.9688 10.9951 21.75 10.9951ZM8.25 10.9951H4.5V7.99512C4.5 6.35449 5.8125 4.99512 7.5 4.99512H7.875C8.48438 4.99512 9 4.52637 9 3.87012V1.62012C9 1.01074 8.48438 0.495117 7.875 0.495117H7.5C3.32812 0.495117 0 3.87012 0 7.99512V19.2451C0 20.5107 0.984375 21.4951 2.25 21.4951H8.25C9.46875 21.4951 10.5 20.5107 10.5 19.2451V13.2451C10.5 12.0264 9.46875 10.9951 8.25 10.9951Z" fill="#da1333"></path>
                                    </svg>
                                </div>
                                {{-- <div class="client-quote-two">
                                    <svg width="300" height="200" viewBox="0 0 300 263" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M271.875 131.5H225V94C225 73.4922 241.406 56.5 262.5 56.5H267.188C274.805 56.5 281.25 50.6406 281.25 42.4375V14.3125C281.25 6.69531 274.805 0.25 267.188 0.25H262.5C210.352 0.25 168.75 42.4375 168.75 94V234.625C168.75 250.445 181.055 262.75 196.875 262.75H271.875C287.109 262.75 300 250.445 300 234.625V159.625C300 144.391 287.109 131.5 271.875 131.5ZM103.125 131.5H56.25V94C56.25 73.4922 72.6562 56.5 93.75 56.5H98.4375C106.055 56.5 112.5 50.6406 112.5 42.4375V14.3125C112.5 6.69531 106.055 0.25 98.4375 0.25H93.75C41.6016 0.25 0 42.4375 0 94V234.625C0 250.445 12.3047 262.75 28.125 262.75H103.125C118.359 262.75 131.25 250.445 131.25 234.625V159.625C131.25 144.391 118.359 131.5 103.125 131.5Z" fill="#F8F7F7"></path>
                                    </svg>
                                </div> --}}
                                <p>“ {{ @$item->language->$defualt->details ?? @$item->language->$default_lng->details }} “</p>
                                <h3 class="title">{{ @$item->language->$defualt->name ?? @$item->language->$default_lng->name }}</h3>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif

                </div>
            </div>
        </div>
    </div>
</section>
