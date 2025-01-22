@php
    $campaign_head = getSectionData('campaigns');
    $defualt = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
@endphp
<section class="campaign-section pt-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 text-center">
                <div class="section-header">
                    {{-- <span class="section-sub-title">{{ __(@$campaign_head->value->language->$defualt->title ?? @$campaign_head->value->language->$default_lng->title) }}</span> --}}
                    @php
                        $header = explode('|', @$campaign_head->value->language->$defualt->heading ?? @$campaign_head->value->language->$default_lng->heading);
                    @endphp
                    <h2 class="section-title">@isset($header[0]) {{ $header[0] }} @endisset <span>@isset($header[1]) {{ $header[1] }} @endisset</span></h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-30-none">
            @foreach ($campaigns as $item)

            @php
                $percent = (($item->raised * 100) / $item->our_goal);
            @endphp

            <div class="col-xl-4 col-lg-6 col-md-6 mb-30">
                <div class="campaign-item">
                    <div class="campaign-thumb">
                        <a href="{{route('campaign.details',[$item->id, $item->slug])}}">
                            <img src="{{ get_image($item->image,'campaigns') }}" alt="campaign">
                            <div class="campaign-category">
                                <span>#{{ __('Life') }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="campaign-content">
                        <h3 class="title"><a href="{{route('campaign.details',[$item->id, $item->slug])}}">{{ @$item->title->language->$defualt->title ?? @$item->title->language->$default_lng->title }}</a></h3>
                        @php
                            $description = strip_tags(@$item->desc->language->$defualt->desc)
                        @endphp
                        <p>{{ Str::limit(@$description, 80); }}</p>
                        <div class="skill-bar">
                            <div class="progressbar" data-perc="{{ $percent }}%">
                                <div class="bar"></div>
                                <span class="label">{{ number_format($percent) }}%</span>
                            </div>
                        </div>
                        <div class="campaign-footer-area">
                            <div class="left">
                                <span class="sub-title">{{ __('Our Goal') }}</span>
                                <h5 class="title">{{ $default_currency->symbol }}{{ get_amount(@$item->our_goal) }}</h5>
                            </div>
                            <div class="center">
                                <span class="sub-title">{{ __('Raised') }}</span>
                                <h5 class="title">{{ $default_currency->symbol }}{{ get_amount(@$item->raised) }}</h5>
                            </div>
                            <div class="right">
                                <span class="sub-title">{{ __('To Go') }}</span>
                                <h5 class="title">{{ $default_currency->symbol }}{{ get_amount(@$item->to_go)
                                 }}</h5>
                            </div>
                        </div>
                        <div class="campaign-btn">
                            <a href="{{route('campaign.details',[$item->id, $item->slug])}}" class="btn--base" @if ($item->to_go == 0)disabled="disabled"@endif>{{ __('Donate Now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
