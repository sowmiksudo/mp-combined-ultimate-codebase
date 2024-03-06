@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Amount') }}</th>
                                <th data-breakpoints="lg">{{ translate('Customer Name') }}</th>
                                <th>{{ translate('Payment Method') }}</th>
                                <th>{{ translate('Account') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th data-breakpoints="lg">{{ translate('Date') }}</th>
                                <th class="text-right" width="10%">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($withdrawals as $key => $withdrawal)
                                <tr>
                                    <td>{{ $key + 1 + ($withdrawals->currentPage() - 1) * $withdrawals->perPage() }}</td>
                                    <td>
                                        {{ $withdrawal->amount }}
                                    </td>
                                    <td>
                                        @if ($withdrawal->user != null)
                                            {{ $withdrawal->user->name }}
                                        @else
                                            {{ translate('User not found') }}
                                        @endif
                                    </td>
                                    <td>{{ $withdrawal->payment_method }}</td>
                                    <td>
                                        {{ $withdrawal->account }}
                                    </td>
                                    <td>
                                        {{ ucfirst($withdrawal->status) }}
                                    </td>
                                    <td>{{ $withdrawal->created_at }}</td>
                                    <td class="text-right">
                                        <form action="{{ route('withdraw_requests.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="req_id" value="{{ $withdrawal->id }}">
                                            <button type="submit" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Mark as paid') }}">
                                                <i class="las la-check-double"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination">
                        {{ $withdrawals->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
