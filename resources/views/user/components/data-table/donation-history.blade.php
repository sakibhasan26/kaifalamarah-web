<table class="custom-table">
    <thead>
        <tr>
            <th>{{ __('Campaign Title') }}</th>
            <th>{{ __('Campaign Goal') }}</th>
            <th>{{ __('Raised') }}</th>
            <th>{{ __('To Go') }}</th>
            <th>{{ __('Donation Amount') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Donation Date') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($donation_histories ?? [] as $item)
        {{-- @dd($item->campaign) --}}
            <tr>
                <td>{{ @$item->campaign->title->language->$default->title ?? @$item->campaign->title->language->$default_lng->title }}</td>
                <td>${{ get_amount(@$item->campaign->our_goal) }}</td>
                <td>${{ get_amount(@$item->campaign->raised) }}</td>
                <td>${{ get_amount(@$item->campaign->to_go) }}</td>
                <td>${{ get_amount(@$item->request_amount) }}</td>
                <td>
                    <span class="badge {{ $item->stringStatus->class }}">{{ $item->stringStatus->value }}</span>
                </td>
                <td>{{ dateFormat('d M Y', $item->created_at) }}</td>
                <td>
                    @if ($item->status == payment_gateway_const()::STATUSWAITING && $item->currency->gateway->alias == 'tatum')
                        <button class="btn btn-success tnx_hash" data-submit_url="{{ setRoute('donation.payment.crypto.confirm', $item->trx_id) }}">{{ __("Tnx hash") }}</button>
                    @endif
                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty',['colspan' => 7])
        @endforelse
    </tbody>
</table>

<div class="modal fade" id="trx_hash_modal" tabindex="-1" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header p-3">
                <h5 class="modal-title">{{ __('Submit Transaction Hash') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="modal-form" action="" method="POST">
                    @csrf
                    <div class="row mb-10-none">
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.input',[
                                'label'         => __('Transaction Hash Hash')."*",
                                'placeholder'   => __('Transaction Hash'),
                                'required'      => 'required',
                                'name'          => 'txn_hash',
                                'erroer_block'  => true,
                                'value'         => old('txn_hash')
                            ])
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn--base">{{ __('Confirm') }}</button>
            </div>
        </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function () {
            @if ($errors->any())
                var modal = $('#trx_hash_modal');
                modal.modal('show');
            @endif
            $('.tnx_hash').on('click', function () {
                var modal = $('#trx_hash_modal');
                var submit_url = $(this).data('submit_url');
                $('#trx_hash_modal .modal-form').attr('action', submit_url);
                modal.modal('show');
            });
        });

    </script>
@endpush
