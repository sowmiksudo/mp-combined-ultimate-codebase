@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container m-3">
                <h4 class="text-center">Start New Live Sale</h4>
                <form action="{{ route('live_sales.store') }}" method="post" class="d-flex justify-content-center">
                    @csrf
                    <div class="col-4 m-2">
                        <select class="form-control aiz-selectpicker" data-live-search="true"
                            data-minimum-results-for-search="Infinity" id="" name="is_running">
                            <option value="">{{ translate('Select Live Status') }}</option>
                            <option value="1" selected>
                                Running
                            </option>
                            <option value="0">
                                Ended
                            </option>
                        </select>
                        <div class="my-1">
                            <input type="text" name="title" id="" placeholder="Live title" class="form-control">
                        </div>
                        <button class="btn btn-primary btn-block my-2">Start Sale</button>
                    </div>
                    <textarea name="embed_code" class="form-control col-5 m-2" id="" cols="30" rows="5"
                        placeholder="Embed code for the live given by facebook"></textarea>

                </form>
            </div>
            <form class="" action="" id="sort_orders" method="GET">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <!--<th>#</th>-->
                            <th>
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-all">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            </th>
                            <th data-breakpoints="md">ID</th>
                            {{-- <th data-breakpoints="md">{{ translate('Num. of Products') }}</th> --}}
                            <th data-breakpoints="md">{{ translate('Title') }}</th>
                            <th data-breakpoints="md">{{ translate('Slug') }}</th>
                            <th data-breakpoints="md">{{ translate('Live status') }}</th>
                            <th data-breakpoints="md">{{ translate('Date') }}</th>
                            <th data-breakpoints="md">{{ translate('Orders') }}</th>
                            <th class="text-right" width="15%">{{ translate('options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lives as $key => $live)
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="id[]"
                                                    value="{{ $live->id }}">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $live->id }}
                                </td>
                                <td>
                                    {{ $live->title }}
                                </td>
                                <td>
                                    {{ $live->slug }}
                                </td>
                                <td>
                                    {{ $live->is_running ? 'Running' : 'Ended' }}
                                </td>
                                <td>
                                    {{ $live->created_at }}
                                </td>
                                <td>
                                    {{ $live->orders->count() }}
                                </td>
                                <td class="text-right">
                                    @can('view_order_details')
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            href="{{ route('live_sales.show', encrypt($live->id)) }}"
                                            title="{{ translate('View') }}">
                                            <i class="las la-eye"></i>
                                        </a>
                                    @endcan
                                    {{-- <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                        href="{{ route('invoice.download', $live->id) }}"
                                        title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a> --}}
                                    @can('delete_order')
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                            data-href="{{ route('live_sales.destroy', $live->id) }}"
                                            title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="aiz-pagination">
                    {{ $lives->appends(request()->input())->links() }}
                </div>

            </form>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        //        function change_status() {
        //            var data = new FormData($('#order_form')[0]);
        //            $.ajax({
        //                headers: {
        //                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                },
        //                url: "{{ route('bulk-order-status') }}",
        //                type: 'POST',
        //                data: data,
        //                cache: false,
        //                contentType: false,
        //                processData: false,
        //                success: function (response) {
        //                    if(response == 1) {
        //                        location.reload();
        //                    }
        //                }
        //            });
        //        }

        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-order-delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection
