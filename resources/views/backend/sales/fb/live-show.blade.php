@extends('backend.layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Live Sale Details') }}</h1>
        </div>
        <div class="card-body">
            <div class="container m-3">
                <form action="{{ route('live_sales.update', encrypt($live->id)) }}" method="post"
                    class="d-flex justify-content-center">
                    @csrf
                    <div class="col-4 m-2">
                        <select class="form-control aiz-selectpicker" data-live-search="true"
                            data-minimum-results-for-search="Infinity" id="" name="is_running">
                            <option value="">{{ translate('Select Live Status') }}</option>
                            <option value="1" @if ($live->is_running === 1) selected @endif>
                                Running
                            </option>
                            <option value="0" @if ($live->is_running === 0) selected @endif>
                                Ended
                            </option>
                        </select>
                        <button class="btn btn-primary btn-block my-2">Save</button>
                    </div>
                    <textarea name="embed_code" class="form-control col-5 m-2" id="" cols="30" rows="5"
                        placeholder="Embed code for the live">{{ $live->embed_code }}</textarea>

                </form>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table-bordered aiz-table invoice-summary table">
                        <thead>
                            <tr class="bg-trans-dark">
                                <th>Id</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Delivery Status</th>
                                <th>Payment Status</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ json_decode($order->customer_info)->name }}</td>
                                    <td>{{ json_decode($order->customer_info)->phone }}</td>
                                    <td>{{ $order->delivery_status }}</td>
                                    <td>
                                        @if ($order->payment_status == 'unpaid')
                                            <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                        @else
                                            <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @can('view_order_details')
                                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                href="{{ route('live_orders.show', encrypt($order->id)) }}"
                                                title="{{ translate('View') }}">
                                                <i class="las la-eye"></i>
                                            </a>
                                        @endcan
                                        {{-- <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                        href="{{ route('invoice.download', $order->id) }}"
                                        title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a> --}}
                                        @can('delete_order')
                                            <a href="#"
                                                class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                data-href="{{ route('orders.destroy', $order->id) }}"
                                                title="{{ translate('Delete') }}">
                                                <i class="las la-trash"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix float-right">
                <table class="table">
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
