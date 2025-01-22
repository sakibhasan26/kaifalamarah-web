@php
    $defualt = get_default_language_code()??'en';
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData( $footer_slug)->first();
    $type = Illuminate\Support\Str::slug(App\Constants\GlobalConst::USEFUL_LINKS);
    $useful_links = App\Models\Admin\SetupPage::where('type',$type)->where('status', 1)->get();
@endphp

<footer class="footer-section section--bg" id="footer-section">
    <div class="footer-shape">
        <img src="{{ asset('public/frontend/') }}/images/footer/footer-shape-2.png" alt="shape">
    </div>
    <div class="container">
        <div class="row mb-30-none">
            <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                <div class="footer-widget">
                    <div class="footer-logo">

                        <a class="site-logo site-title" href="{{ setRoute('index') }}">
                            <img src="{{ get_logo($basic_settings,'dark') }}" alt="site-logo">
                        </a>
                    </div>
                    <p>{{ __(@$footer->value->language->$defualt->details) }}</p>
                    <div class="social-area">
                        <span>{{ __('Follow Us') }}:</span>
                        <ul class="footer-social">
                            @if(isset($footer->value->items))
                                @foreach($footer->value->items ?? [] as $key => $item)
                                    <li><a href="{{ @$item->language->$defualt->link }}" target="_blank"><i class=" {{ @$item->language->$defualt->social_icon }}"></i></a></li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-6 mb-30">
                <div class="footer-widget">
                    <h3 class="title">{{ __('Support Us') }}</h3>
                    <ul class="footer-list">
                        <li><a href="{{ setRoute('events') }}">{{ __('Events') }} <i class="las la-arrow-right"></i></a></li>
                        <li><a href="{{ setRoute('donation') }}">{{ __('Donation') }} <i class="las la-arrow-right"></i></a></li>
                        <li><a href="{{ setRoute('gallery') }}">{{ __('Gallery') }} <i class="las la-arrow-right"></i></a></li>
                        <li><a href="{{setRoute('faq') }}">{{ __('FAQ') }} <i class="las la-arrow-right"></i></a></li>
                        <li><a href="{{setRoute('download.app') }}">{{ __('Download App') }} <i class="las la-arrow-right"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-xl-2 col-lg-4 col-md-6 mb-30">
                <div class="footer-widget">
                    <h3 class="title">{{ __('Useful Links') }}</h3>
                    <ul class="footer-list">
                        @foreach ($useful_links as $item)
                            <li><a href="{{route('page.view',$item->slug)}}">{{ @$item->title->language->$defualt->title }} <i class="las la-arrow-right"></i></a></li>
                        @endforeach

                    </ul>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-6 mb-30">
                <div class="footer-widget">
                    <h3 class="title">{{ __('Newsletter') }}</h3>
                    <p>{{ __(@$footer->value->language->$defualt->newsltter_details) }}</p>
                    <form class="newsletter-form" method="POST" id="newsletter-form">
                        @csrf
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form--control" placeholder="{{ __('Your email') }}" required>
                        </div>
                        <button type="submit" class="btn--base white"><i class="fa fa-spinner d-none fa-pulse fa-fw"></i> {{ __('Subscribe') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="copyright-area">
            <p>{{ __(@$footer->value->language->$defualt->footer_text) }} <a href="{{ url('/') }}"><span>{{ $basic_settings->site_name ?? '' }}.</span></a></p>
        </div>
    </div>
</footer>
