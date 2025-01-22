@php
    $defualt = get_default_language_code()??'en';
    $default_lng = 'en';
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
    $footer_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FOOTER_SECTION);
    $footer = App\Models\Admin\SiteSections::getData($footer_slug)->first();
@endphp
<div class="sidebar-menu">
    <div class="sidebar-menu-inner">
        <div class="logo-env">
            <div class="logo">
                <a href="{{ route('index') }}">
                    <img src="{{ get_logo($basic_settings,"dark") }}" width="140"  alt="logo">
                </a>
            </div>
            <div class="sidebar-collapse">
                <a href="javasctipt:void(0)" class="sidebar-collapse-icon">
                    <i class="las la-bars"></i>
                </a>
            </div>
            <div class="sidebar-mobile-menu">
                <a href="#" class="with-animation">
                    <i class="las la-bars"></i>
                </a>
            </div>
        </div>
        <ul id="sidebar-main-menu" class="sidebar-main-menu">
            <li class="sidebar-single-menu nav-item {{ menuActive('user.dashboard') }} open">
                <a href="{{ route('user.dashboard') }}">
                    <i class="las la-radiation-alt"></i>
                    <span class="title">{{ __('Dashboard') }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu nav-item {{ menuActive('user.add.money.index') }} open">
                <a href="{{ route('user.add.money.index') }}">
                    <i class="las la-sign"></i>
                    <span class="title">{{ __('Add Money') }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu nav-item {{ menuActive('user.donation.history') }} open">
                <a href="{{ route('user.donation.history') }}">
                    <i class="las la-history"></i>
                    <span class="title">{{ __('Donation History') }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu nav-item">
                <a href="{{ route('about') }}">
                    <i class="las la-address-card"></i>
                    <span class="title">{{ __('About') }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu nav-item">
                <a href="{{ route('campaign') }}">
                    <i class="las la-list"></i>
                    <span class="title">{{ __('Donate') }}</span>
                </a>
            </li>
            {{-- <li class="sidebar-single-menu nav-item">
                <a href="campaign-details.html">
                    <i class="las la-donate"></i>
                    <span class="title">Donation</span>
                </a>
            </li> --}}
            <li class="sidebar-single-menu nav-item">
                <a href="{{ route('events') }}">
                    <i class="las la-calendar-check"></i>
                    <span class="title">{{ __('Event') }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu nav-item">
                <a href="{{ route('gallery') }}" >
                    <i class="las la-image"></i>
                    <span class="title">{{ __('Gallery') }}</span>
                </a>
            </li>
            <li class="sidebar-single-menu nav-item">
                <a href="{{ route('download.app') }}">
                    <i class="lab la-app-store"></i>
                    <span class="title">{{ __('Download App') }}</span>
                </a>
            </li>
        </ul>
        <div class="footer-area text-center">
            <div class="social-area justify-content-center">
                <ul class="footer-social">
                    @foreach ($footer->value->items ?? [] as $item)
                        <li><a href="{{ @$item->language->$defualt->link }}" target="_blank" title="{{ @$item->language->$defualt->title }}"><i class="{{ $item->language->$defualt->social_icon ??  'fab fa-facebook-f'}}"></i></a></li>
                    @endforeach
                    {{-- <li><a href="#0" class="active"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#0"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#0"><i class="fab fa-linkedin-in"></i></a></li> --}}
                </ul>
            </div>
            <p>{{ __(@$footer->value->language->$defualt->footer_text) }}</p>
        </div>
    </div>
</div>

@push('script')
    <script>
         $(".logout-btn").click(function(){
            var actionRoute =  "{{ setRoute('user.logout') }}";
            var target      = 1;
            var logout = `{{ __("Logout") }}`;
            var sureText = '{{ __("ru sure to") }}';
            var message     = `${sureText} <strong>${logout}</strong>?`;
            openAlertModal(actionRoute,target,message,logout,"POST");
        });

    </script>
@endpush
