@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Purchase History') }}</h5>
        </div>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th data-breakpoints="md">{{ translate('Date') }}</th>
                            <th>{{ translate('Product') }}</th>
                            <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                            <th>{{ translate('Amount') }}</th>
                            <th data-breakpoints="md">{{ translate('Commission') }}</th>
                            <th class="text-right">{{ translate('Your Earning') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($preloved_orderDetails as $key => $order)
                            <tr>
                                <td>{{ $order->created_at->toDateTimeString() }}</td>
                                <td>
                                    <a href="{{ route('product', \App\Models\Product::find($order->product_identifier)->slug) }}">{{ \App\Models\Product::find($order->product_identifier)->name }}</a>
                                </td>
                                <td>
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->order->delivery_status))) }}
                                </td>
                                <td>
                                    {{ single_price($order->price) }}
                                </td>
                                <td>
                                    {{ \App\Models\Product::find($order->product_identifier)->preloved_commission }}%
                                </td>
                                <td>
                                    ৳{{ $order->price - ((\App\Models\Product::find($order->product_identifier)->preloved_commission / 100) * $order->price) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- <div class="aiz-pagination">
                    {{ $orders->links() }}
                </div> --}}
            </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function() {
            location.reload();
        })
    </script>
@endsection
