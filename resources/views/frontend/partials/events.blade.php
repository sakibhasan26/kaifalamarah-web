@php
    $default = get_default_language_code()??'en';
    $default_lng = App\Constants\LanguageConst::NOT_REMOVABLE;
@endphp
<section class="blog-section ptb-120">
    <div class="container">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xl-8 col-lg-7 mb-30">
                <div class="row justify-content-center mb-30-none">
                    @foreach ($events as $item)
                    <div class="col-xl-12 mb-30">
                        <div class="blog-item">
                            <div class="blog-thumb">
                                <a href="{{ setRoute('events.details',[$item->id, $item->slug])}}"><img src="{{ get_image($item->image,'events') }}" alt="{{ @$item->title->language->$default->title }}"></a>
                            </div>
                            <div class="blog-content">
                                <div class="blog-date">
                                    <h6 class="title">{{ dateFormat('d M',$item->created_at) }}</h6>
                                    <span class="sub-title">{{ dateFormat('Y',$item->created_at) }}</span>
                                </div>
                                <span class="category">{{ @$item->category->data->language->$default->name ?? @$item->category->data->language->$default_lng->name }}</span>
                                <h3 class="title"><a href="{{ setRoute('events.details',[$item->id, $item->slug])}}">{{ @$item->title->language->$default->title ?? $item->title->language->$default_lng->title }}</a></h3>
                                <p>{!! Str::limit(@$item->details->language->$default->details ?? @$item->details->language->$default_lng->details, 150); !!}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @include('frontend.partials.events_sidebar', compact('categories', 'recent_events'))
        </div>
    </div>
</section>
