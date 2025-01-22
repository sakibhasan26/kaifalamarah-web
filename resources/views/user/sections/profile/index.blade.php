@extends('user.layouts.master')

@push('css')

@endpush

@section('breadcrumb')
    @include('user.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("user.dashboard"),
        ]
    ], 'active' => __("profile")])
@endsection

@section('content')
    <div class="card-area pt-60">
        <div class="row justify-content-center mb-30-none">
            <div class="col-xxl-6 col-xl-12 col-lg-12 mb-30">
                <div class="card custom--card">
                    <div class="card-form-wrapper">
                        <form class="role" method="POST" action="{{ setRoute('user.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method("PUT")
                            <div class="profile-settings-wrapper">
                                <div class="preview-thumb profile-wallpaper">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview bg-overlay-base bg_img"
                                            data-background="{{ asset('public/frontend/') }}/images/site-section/15fe64bf-45fa-4cac-bdde-47cba49c38b3.webp"></div>
                                    </div>
                                </div>
                                <div class="profile-thumb-content">
                                    <div class="preview-thumb profile-thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview bg_img" data-background="{{ $user->userImage }}">
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type='file' class="profilePicUpload" name="image" id="profilePicUpload2"
                                                accept=".png, .jpg, .jpeg" />
                                            <label for="profilePicUpload2"><i class="las la-pen"></i></label>
                                        </div>
                                    </div>
                                    <div class="profile-content">
                                        <h6 class="username">Usernamehgygt22</h6>
                                        <ul class="user-info-list mt-md-2">
                                            <li><i class="las la-user"></i>{{ $user->username??'' }}</li>
                                            <li><i class="las la-envelope"></i>{{ $user->email??'' }}</li>
                                            <li><i class="las la-phone"></i> {{ $user->full_mobile??"Not Added Yet" }}</li>
                                            <li><i class="las la-map-marked-alt"></i> {{ $user->address->country??"Not Added Yet" }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-form-wrapper">

                                        <div class="row justify-content-center mb-10-none">
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __("first Name")."*",
                                                    'name'          => "firstname",
                                                    'placeholder'   => __("Enter First Name"),
                                                    'value'         => old('firstname',auth()->user()->firstname)
                                                ])
                                            </div>
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __("last Name")."*",
                                                    'name'          => "lastname",
                                                    'placeholder'   => __("Enter Last Name"),
                                                    'value'         => old('lastname',auth()->user()->lastname)
                                                ])
                                            </div>
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                <label>{{ __("Country") }}</label>
                                                <select name="country" class="form-control select2-auto-tokenize country-select" data-placeholder="Select Country" data-old="{{ old('country',auth()->user()->address->country ?? "") }}"></select>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                <label>{{ __("Phone") }}</label>
                                                <div class="input-group mb-0">
                                                    <div class="input-group-text phone-code">+{{ auth()->user()->mobile_code }}</div>
                                                    <input class="phone-code" type="hidden" name="phone_code" value="{{ auth()->user()->mobile_code }}" />
                                                    <input type="text" class="form--control" placeholder="{{ __("Enter Phone") }}" name="phone" value="{{ old('phone',auth()->user()->mobile) }}">
                                                </div>
                                                @error("phone")
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __("State"),
                                                    'name'          => "state",
                                                    'placeholder'   => __("Enter State"),
                                                    'value'         => old('state',auth()->user()->address->state?? "")
                                                ])
                                            </div>
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __("City"),
                                                    'name'          => "city",
                                                    'placeholder'   => __("Enter city"),
                                                    'value'         => old('city',auth()->user()->address->city?? "")
                                                ])
                                            </div>
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __("Zip Code"),
                                                    'name'          => "zip_code",
                                                    'placeholder'   => __("Enter Zip"),
                                                    'value'         => old('zip_code',auth()->user()->address->zip ?? "")
                                                ])
                                            </div>
                                            <div class="col-xl-6 col-lg-6 form-group">
                                                @include('admin.components.form.input',[
                                                    'label'         => __("Address"),
                                                    'name'          => "address",
                                                    'placeholder'   => __("Enter Address"),
                                                    'value'         => old('address',auth()->user()->address->address ?? "")
                                                ])
                                            </div>
                                            <div class="col-xl-12 form-group">
                                                <button type="submit" class="btn--base mt-10">{{ __('Update') }}</button>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        getAllCountries("{{ setRoute('global.countries') }}");
        $(document).ready(function(){
            $("select[name=country]").change(function(){
                var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCode);
            });

            countrySelect(".country-select",$(".country-select").siblings(".select2"));
            stateSelect(".state-select",$(".state-select").siblings(".select2"));
        });
    </script>
@endpush
