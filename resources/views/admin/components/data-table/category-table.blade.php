<table class="custom-table currency-search-table">
    <thead>
        <tr>

            <th>{{ __("Category Name") }}</th>
            <th></th>
            <th>{{ __("Category Type") }}</th>
            <th></th>
            <th>{{ __("Status") }}</th>
            <th>{{ __("Action") }}</th>
        </tr>
    </thead>
    <tbody>

        @forelse ($allCategory ?? [] as $item)
            <tr data-item="{{ json_encode($item) }}">

                <td>{{ $item->data?->language?->$app_local?->name ?? '' }}</td>

                <td></td>
                <td>@if($item->type == 1)
                    <span class="text--info">{{ __("FAQ") }}</span>
                    @elseif($item->type == 2)
                    <span class="text--info">{{ __("Event") }}</span>
                @endif</td>
                <td></td>

                <td>
                    @include('admin.components.form.switcher',[
                        'name'          => 'category_status',
                        'value'         => $item->status,
                        'options'       => [__('Enable') => 1,__('Disable') => 0],
                        'onload'        => true,
                        'data_target'   => $item->id,
                        'permission'    => "admin.setup.sections.category.status.update",
                    ])
                </td>
                <td>
                    @include('admin.components.link.edit-default',[
                        'href'          => "javascript:void(0)",
                        'class'         => "edit-modal-button",
                        'permission'    => "admin.currency.update",
                    ])

                    @include('admin.components.link.delete-default',[
                        'href'          => "javascript:void(0)",
                        'class'         => "delete-modal-button",
                        'permission'    => "admin.setup.sections.category.delete",
                    ])

                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty',['colspan' => 7])
        @endforelse
    </tbody>
</table>

@push("script")
    <script>
        $(document).ready(function(){
            // Switcher
            switcherAjax("{{ setRoute('admin.setup.sections.category.status.update') }}");
        })
    </script>
@endpush
