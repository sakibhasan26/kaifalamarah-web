
<div class="body-header-area d-flex flex-wrap align-items-center justify-content-between mb-10-none">

       @yield('breadcrumb')

    <div class="body-header-right dropdown">
        <button type="button" data-bs-toggle="dropdown" data-display="static" aria-haspopup="true"
            aria-expanded="false">
            <div class="header-user-area">
                <div class="header-user-thumb">
                    <a href="javascript:void(0)"><img src="{{ auth()->user()->userImage }}" alt="client"></a>
                </div>
            </div>
        </button>
        <div class="dropdown-menu dropdown-menu--sm p-0 border-0 dropdown-menu-right">
            <a href="{{ setRoute('user.profile.change.password') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                <i class="dropdown-menu__icon las la-key"></i>
                <span class="dropdown-menu__caption">{{ __('Change Password') }}</span>
            </a>
            <a href="{{ setRoute('user.profile.index') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                <i class="dropdown-menu__icon las la-user-circle"></i>
                <span class="dropdown-menu__caption">{{ __('Profile Settings') }}</span>
            </a>
            <a href="javascript:void(0)" class="logout-btn dropdown-menu__item d-flex align-items-center px-3 py-2">
                <i class="dropdown-menu__icon las la-power-off"></i>
                <span class="dropdown-menu__caption">{{ __('Logout') }}</span>
            </a>
            <a href="javascript:void(0)" class="delete-btn dropdown-menu__item d-flex align-items-center px-3 py-2">
                <i class="dropdown-menu__icon las la-trash"></i>
                <span class="dropdown-menu__caption">{{ __('Delete Account') }}</span>
            </a>
        </div>
    </div>
</div>
@push('script')
    <script>

        $(".delete-btn").click(function(){
            var actionRoute =  "{{ setRoute('user.delete.account') }}";
            var target      = 1;
            var btnText = "Delete Account";
            var projectName = "{{ @$basic_settings->site_name }}";
            var name = $(this).data('name');
            var message     = `Are you sure to delete <strong>your account</strong>?<br>If you do not think you will use “<strong>${projectName}</strong>”  again and like your account deleted, we can take card of this for you. Keep in mind you will not be able to reactivate your account or retrieve any of the content or information you have added. If you would still like your account deleted, click “Delete Account”.?`;
            openAlertModal(actionRoute,target,message,btnText,"DELETE");
        });

    </script>
@endpush

