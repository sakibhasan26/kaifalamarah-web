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
    ], 'active' => __("Donation")])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __($page_title) }}</h6>
        </div>

        <div class="card-body">
            <form class="card-form" action="{{ setRoute('admin.campaigns.update') }}" method="POST" enctype="multipart/form-data">
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
                                    {{-- <div class="form-group">
                                        @include('admin.components.form.input',[
                                            'label'     => __('Title')."*",
                                            'name'      => $default_lang_code . "_title",
                                            'value'     => old($default_lang_code . "_title",$head_data->value->language->$default_lang_code->title ?? "")
                                        ])
                                    </div> --}}

                                    <div class="form-group">
                                        @include('admin.components.form.input',[
                                            'label'     => __("Heading")."*",
                                            'name'      => $default_lang_code . "_heading",
                                            'value'     => old($default_lang_code . "_heading",$head_data->value->language->$default_lang_code->heading ?? "")
                                        ])
                                    </div>

                                </div>

                                @foreach ($languages as $item)
                                    @php
                                        $lang_code = $item->code;
                                    @endphp
                                    <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif" id="{{ $item->name }}" role="tabpanel" aria-labelledby="english-tab">
                                        {{-- <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Title")."*",
                                                'name'      => $lang_code . "_title",
                                                'value'     => old($lang_code . "_title",$head_data->value->language->$lang_code->title ?? "")
                                            ])
                                        </div> --}}

                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Heading")."*",
                                                'name'      => $lang_code . "_heading",
                                                'value'     => old($lang_code . "_heading",$head_data->value->language->$lang_code->heading ?? "")
                                            ])
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
                            'permission'    => "admin.campaigns.update"
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-area mt-15">
        <div class="table-wrapper">
            <div class="table-header justify-content-end">
                <div class="table-btn-area">
                    <a href="#campaign-add" class="btn--base modal-btn"><i class="fas fa-plus me-1"></i> {{ __("Add Donation") }}</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Our Goal') }}</th>
                            <th>{{ __('Raised') }}</th>
                            <th>{{ __('To Go') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data ?? [] as $key => $item)
                            <tr data-item="{{ json_encode($item) }}">
                                <td>
                                    <ul class="user-list">
                                        <li><img src="{{ get_image($item->image,"campaigns") }}" alt="Campaign Image"></li>
                                    </ul>
                                </td>
                                <td>
                                    {{ $item->title->language->$system_default_lang->title ?? $item->title->language->$default_lang_code->title }}
                                </td>
                                <td>
                                    {{ number_format($item->our_goal) ?? "" }}
                                </td>
                                <td>
                                    {{ number_format($item->raised) ?? "" }}
                                </td>
                                <td>
                                    {{ number_format($item->to_go) ?? "" }}
                                </td>
                                <td>
                                    @include('admin.components.form.switcher',[
                                        'name'          => 'campaign_status',
                                        'value'         => $item->status,
                                        'options'       => [__('Enable') => 1,__('Disable') => 0],
                                        'onload'        => true,
                                        'data_target'   => $item->id,
                                        'permission'    => "admin.campaigns.items.status.update",
                                    ])
                                </td>
                                <td>
                                    <a href="{{ setRoute('admin.campaigns.edit', $item->id) }}" class="btn btn--base"><i
                                        class="las la-pencil-alt"></i></a>
                                    <button class="btn btn--base btn--danger delete-modal-button" ><i class="las la-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            @include('admin.components.alerts.empty',['colspan' => 7])
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.components.modals.site-section.campaign-add')


    {{--  Item Edit Modal --}}
    <div id="campaign-edit" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __("Edit Donation") }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST" action="{{ setRoute('admin.campaigns.items.update') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="target" value="{{ old('target') }}">
                    <div class="row mb-10-none mt-3">
                        <div class="language-tab">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link @if (get_default_language_code() == language_const()::NOT_REMOVABLE) active @endif" id="edit-modal-english-tab" data-bs-toggle="tab" data-bs-target="#edit-modal-english" type="button" role="tab" aria-controls="edit-modal-english" aria-selected="false">English</button>
                                    @foreach ($languages as $item)
                                        <button class="nav-link @if (get_default_language_code() == $item->code) active @endif" id="edit-modal-{{$item->name}}-tab" data-bs-toggle="tab" data-bs-target="#edit-modal-{{$item->name}}" type="button" role="tab" aria-controls="edit-modal-{{ $item->name }}" aria-selected="true">{{ $item->name }}</button>
                                    @endforeach

                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane @if (get_default_language_code() == language_const()::NOT_REMOVABLE) fade show active @endif" id="edit-modal-english" role="tabpanel" aria-labelledby="edit-modal-english-tab">
                                    @php
                                        $default_lang_code = language_const()::NOT_REMOVABLE;
                                    @endphp
                                    <div class="row">
                                        <div class="col-xl-8">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Title")."*",
                                                    'name'      => $default_lang_code . "_title",
                                                    'value'     => old($default_lang_code . "_title")
                                                ])
                                            </div>
                                        </div>
                                        <div class="col-xl-4">
                                            <div class="form-group">
                                                @include('admin.components.form.input',[
                                                    'label'     => __("Our Goal")."*",
                                                    'name'      => "our_goal",
                                                    'value'     => old("our_goal",$data->our_goal ?? "")
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __("Description") }}*</label>
                                        <textarea name="{{ $default_lang_code . "_desc" }}" class="form--control"></textarea>
                                    </div>

                                </div>

                                @foreach ($languages as $item)
                                    @php
                                        $lang_code = $item->code;
                                    @endphp
                                    <div class="tab-pane @if (get_default_language_code() == $lang_code) fade show active @endif" id="edit-modal-{{ $item->name }}" role="tabpanel" aria-labelledby="edit-modal-{{$item->name}}-tab">
                                        <div class="form-group">
                                            @include('admin.components.form.input',[
                                                'label'     => __("Title")."*",
                                                'name'      => $lang_code . "_title",
                                                'value'     => old($lang_code . "_title",$data->value->language->$lang_code->title ?? "")
                                            ])
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __("Description") }}</label>
                                            <textarea name="{{ $lang_code . "_desc" }}" class="form--control d-none">{!! old($lang_code . "_desc",$data->value->language->$lang_code->desc ?? "") !!}</textarea>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input-file',[
                                'label'             => __("Image"),
                                'name'              => "image",
                                'class'             => "file-holder",
                                'old_files_path'    => files_asset_path("campaigns"),
                                'old_files'         => old("old_image"),
                            ])
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __("cancel") }}</button>
                            <button type="submit" class="btn btn--base">{{ __("Update") }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('script')

    <script>
        openModalWhenError("campaign-add","#campaign-add");
        // openModalWhenError("campaign-edit","#campaign-edit");

        var default_language = "{{ $default_lang_code }}";
        var system_default_language = "{{ $system_default_lang }}";
        var languages = "{{ $languages_for_js_use }}";
        languages = JSON.parse(languages.replace(/&quot;/g,'"'));

        $(".edit-modal-button").click(function(){
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));
            var editModal = $("#campaign-edit");

            editModal.find("form").first().find("input[name=target]").val(oldData.id);
            editModal.find("input[name="+default_language+"_title]").val(oldData.title.language[default_language].title);
            editModal.find("input[name='our_goal']").val(parseInt(oldData.our_goal));

            editModal.find("textarea[name="+default_language+"_desc]").val(oldData.desc.language[default_language].desc);
            richTextEditorReinit(document.querySelector("#campaign-edit textarea[name="+default_language+"_desc]"));

            $.each(languages,function(index,item) {
                editModal.find("input[name="+item.code+"_title]").val((oldData.title.language[item.code] == undefined) ? "" : oldData.title.language[item.code].title);
                editModal.find("textarea[name="+item.code+"_desc]").val((oldData.desc.language[item.code] == undefined) ? "" : oldData.desc.language[item.code].desc);

                richTextEditorReinit(document.querySelector("#campaign-edit textarea[name="+item.code+"_desc]"));
            });

            editModal.find("input[name=image]").attr("data-preview-name",oldData.image);

            fileHolderPreviewReInit("#campaign-edit input[name=image]");
            openModalBySelector("#campaign-edit");

        });

        $(".delete-modal-button").click(function(){
            var oldData = JSON.parse($(this).parents("tr").attr("data-item"));

            var actionRoute =  "{{ setRoute('admin.campaigns.items.delete') }}";
            var target = oldData.id;

            var message     = `Are you sure to <strong>delete</strong> item?`;

            openDeleteModal(actionRoute,target,message);
        });

        // Switcher
        switcherAjax("{{ setRoute('admin.campaigns.items.status.update') }}");

    </script>
@endpush
