<table class="custom-table user-search-table">
    <thead>
        <tr>
            <th></th>
            <th>{{ __('Username') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Email Verified Status') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users ?? [] as $key => $item)
            <tr>
                <td>
                    <ul class="user-list">
                        <li><img src="{{ $item->userImage }}" alt="user"></li>
                    </ul>
                </td>
                <td><span>{{ $item->username }}</span></td>
                <td>{{ $item->email }}</td>
                <td>
                    <span class="{{ $item->stringStatus->class }}">{{ $item->stringStatus->value }}</span>
                </td>
                <td>
                    <span class="{{ $item->StringEmailVerifiedStatus->class }}">{{ $item->StringEmailVerifiedStatus->value }}</span>
                </td>
                <td>
                    @if (Route::currentRouteName() == "admin.users.kyc.unverified")
                        @include('admin.components.link.info-default',[
                            'href'          => setRoute('admin.users.kyc.details', $item->username),
                            'permission'    => "admin.users.kyc.details",
                        ])
                    @else
                        @include('admin.components.link.info-default',[
                            'href'          => setRoute('admin.users.details', $item->username),
                            'permission'    => "admin.users.details",
                        ])
                    @endif
                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty',['colspan' => 6])
        @endforelse
    </tbody>
</table>
