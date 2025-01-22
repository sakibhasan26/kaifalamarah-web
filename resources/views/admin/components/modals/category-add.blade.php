@if (admin_permission_by_name("admin.setup.sections.category.store"))
    <div id="category-add" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __("Add New Category") }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST" action="{{ setRoute('admin.setup.sections.category.store') }}">
                    @csrf
                    <div class="row mb-10-none">
                        <div class="col-xl-12 col-lg-12 form-group mt-2">
                            <div class="product-tab">
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        @foreach ($languages as $item)
                                            <button class="nav-link @if (get_default_language_code() == $item->code) active @endif" id="{{$item->name}}-tab" data-bs-toggle="tab" data-bs-target="#{{$item->name}}" type="button" role="tab" aria-controls="{{ $item->name }}" aria-selected="true">{{ $item->name }}</button>
                                        @endforeach
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    @foreach ($languages as $item)
                                        @php
                                            $lang_code = $item->code;
                                        @endphp
                                        <div class="tab-pane @if (get_default_language_code() == $item->code) fade show active @endif" id="{{ $item->name }}" role="tabpanel" aria-labelledby="english-tab">
                                            <div class="col-xl-12 col-lg-12 form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __('Name'),
                                                    'label_after'   => '*',
                                                    'name'          => $item->code . "_name",
                                                    'value'         => old($item->code . "_name")
                                                ])
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 col-lg-12 form-group mt-2">
                            <label for="type">{{ __("Category Type") }}</label>
                            <select name="type" id="type" class="form--control" required>
                                <option value="1">{{__("FAQ")}}</option>
                                <option value="2">{{__("Event")}}</option>
                            </select>
                        </div>

                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __("cancel") }}</button>
                            <button type="submit" class="btn btn--base">{{ __("Add") }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            openModalWhenError("category-add","#category-add");
        </script>
    @endpush
@endif
