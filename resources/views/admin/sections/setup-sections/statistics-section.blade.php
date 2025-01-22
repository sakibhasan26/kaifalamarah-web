@php
    $default_lang_code = language_const()::NOT_REMOVABLE;
    $system_default_lang = get_default_language_code();
    $languages_for_js_use = $languages->toJson();
@endphp

@extends('admin.layouts.master')

@push('css')
    <link rel="stylesheet" href="{{ asset('public/backend/css/fontawesome-iconpicker.min.css') }}">
    <style>
        .fileholder {
            min-height: 374px !important;
        }

        .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,.fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view{
            height: 330px !important;
        }
    </style>
@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ]
    ], 'active' => __("Setup Section")])
@endsection

@section('content')

<div class="custom-card">
    <div class="card-header">
        <h6 class="title">{{ __($page_title) }}</h6>
    </div>
    <div class="card-body">
        <form class="card-form" action="{{ setRoute('admin.setup.sections.section.update',$slug) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row justify-content-center mb-10-none">

                <div class="col-xl-12 col-lg-12">
                    <div class="product-tab">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link @if (get_default_language_code() == language_const()::NOT_REMOVABLE) active @endif" id="english-tab" data-bs-toggle="tab" data-bs-target="#english" type="button" role="tab" aria-controls="english" aria-selected="false">English</button>
                                @foreach ($languages as $item)
                                    <button class="nav-link @if (get_default_language_code() == $item->code) active @endif" id="{{$item->name}}-tab" data-bs-toggle="tab" data-bs-target="#{{$item->name}}" type="button" role="tab" aria-controls="{{ $item->name }}" aria-selected="true">{{ $item->name }}</button>
                                @endforeach
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane @if (get_default_language_code() == language_const()::NOT_REMOVABLE) fade show active @endif" id="english" role="tabpanel" aria-labelledby="english-tab">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Volunteers Icon")."*",
                                                'name'      => $default_lang_code . "_volunteers_icon",
                                                'value'     => old($default_lang_code . "_volunteers_icon",$data->value->language->$default_lang_code->volunteers_icon ?? ""),
                                                'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                            ])
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Volunteers")."*",
                                                'name'      => $default_lang_code . "_volunteers",
                                                'value'     => old($default_lang_code . "_volunteers",$data->value->language->$default_lang_code->volunteers ?? "")
                                            ])
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Donations Icon")."*",
                                                'name'      => $default_lang_code . "_donations_icon",
                                                'value'     => old($default_lang_code . "_donations_icon",$data->value->language->$default_lang_code->donations_icon ?? ""),
                                                'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                            ])
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Donations")."*",
                                                'name'      => $default_lang_code . "_donations",
                                                'value'     => old($default_lang_code . "_donations",$data->value->language->$default_lang_code->donations ?? "")
                                            ])
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Followers Icon")."*",
                                                'name'      => $default_lang_code . "_followers_icon",
                                                'value'     => old($default_lang_code . "_followers_icon",$data->value->language->$default_lang_code->followers_icon ?? ""),
                                                'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                            ])
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Followers")."*",
                                                'name'      => $default_lang_code . "_followers",
                                                'value'     => old($default_lang_code . "_followers",$data->value->language->$default_lang_code->followers ?? "")
                                            ])
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Likes Icon")."*",
                                                'name'      => $default_lang_code . "_likes_icon",
                                                'value'     => old($default_lang_code . "_likes_icon",$data->value->language->$default_lang_code->likes_icon ?? ""),
                                                'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                            ])
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Likes")."*",
                                                'name'      => $default_lang_code . "_likes",
                                                'value'     => old($default_lang_code . "_likes",$data->value->language->$default_lang_code->likes ?? "")
                                            ])
                                        </div>
                                    </div>

                                </div>
                            </div>

                            @foreach ($languages as $item)
                                @php
                                    $lang_code = $item->code;
                                @endphp
                                <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif" id="{{ $item->name }}" role="tabpanel" aria-labelledby="english-tab">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Volunteers Icon")."*",
                                                    'name'      => $lang_code . "_volunteers_icon",
                                                    'value'     => old($lang_code . "_volunteers_icon",$data->value->language->$lang_code->volunteers_icon ?? ""),
                                                    'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Volunteers")."*",
                                                    'name'      => $lang_code . "_volunteers",
                                                    'value'     => old($lang_code . "_volunteers",$data->value->language->$lang_code->volunteers ?? "")
                                                ])
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Donations Icon")."*",
                                                    'name'      => $lang_code . "_donations_icon",
                                                    'value'     => old($lang_code . "_donations_icon",$data->value->language->$lang_code->donations_icon ?? ""),
                                                    'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Donations")."*",
                                                    'name'      => $lang_code . "_donations",
                                                    'value'     => old($lang_code . "_donations",$data->value->language->$lang_code->donations ?? "")
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Followers Icon")."*",
                                                    'name'      => $lang_code . "_followers_icon",
                                                    'value'     => old($lang_code . "_followers_icon",$data->value->language->$lang_code->followers_icon ?? ""),
                                                    'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Followers")."*",
                                                    'name'      => $lang_code . "_followers",
                                                    'value'     => old($lang_code . "_followers",$data->value->language->$lang_code->followers ?? "")
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Likes Icon")."*",
                                                    'name'      => $lang_code . "_likes_icon",
                                                    'value'     => old($lang_code . "_likes_icon",$data->value->language->$lang_code->likes_icon ?? ""),
                                                    'class'     => "form--control icp icp-auto iconpicker-element iconpicker-input",
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Likes")."*",
                                                    'name'      => $lang_code . "_likes",
                                                    'value'     => old($lang_code . "_likes",$data->value->language->$lang_code->likes ?? "")
                                                ])
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 form-group">
                    @include('admin.components.button.form-btn',[
                        'class'         => "w-100 btn-loading",
                        'text'          => __("submit"),
                        'permission'    => "admin.setup.sections.section.update"
                    ])
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@push('script')
    <script src="{{ asset('public/backend/js/fontawesome-iconpicker.js') }}"></script>
    <script>
        // icon picker
        $('.icp-auto').iconpicker();


    </script>
@endpush
