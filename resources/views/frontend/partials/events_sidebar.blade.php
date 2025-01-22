<div class="col-xl-4 col-lg-5 mb-30">
    <div class="sidebar">
        <div class="widget-box mb-30">
            <div class="widget-title-area">
                <h4 class="widget-title">{{ __("Categories") }}</h4>
            </div>
            <div class="category-widget-box">
                <ul class="category-list">
                    @foreach ($categories ?? [] as $cat)
                        <li><a href="javascript:void(0)">{{ @$cat->data->language->$default->name ?? @$cat->data->language->$default_lng->name }} <span class="p-2">{{ isset($cat->events) ? count($cat->events) : '0' }}</span></a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="widget-box mb-30">
            <h4 class="widget-title">{{ __('Recent Posts') }}</h4>
            <div class="popular-widget-box">
                @foreach ($recent_events as $item)
                    <div class="single-popular-item d-flex flex-wrap align-items-center">
                        <div class="popular-item-thumb">
                            <a href="{{ route('events.details',[$item->id, $item->slug])}}"><img src="{{ get_image($item->image,'events') }}" alt="blog"></a>
                        </div>
                        <div class="popular-item-content">
                            <span class="date">{{ dateFormat('d M, Y', $item->created_at) }}</span>
                            <h5 class="title"><a href="{{ route('events.details',[$item->id, $item->slug])}}">{{ Str::limit(@$item->title->language->$default->title ?? @$item->title->language->$default_lng->title, 45, '...') }}</a></h5>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
