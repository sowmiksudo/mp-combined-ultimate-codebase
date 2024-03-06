@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Manage Birthday') }}</h1>
            </div>
        </div>
    </div>
    <form action="{{ route('dob.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Basic Info-->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Birthdate Info') }}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">{{ translate('Your birthdate') }}</label>
                    <div class="col-md-10">
                        @php
                            $date = '';
                            if($user->dob_at != null){
                                $date = $user->dob_at->toDateString();
                            } else {
                                $date = '';
                            }
                        @endphp
                        <input type="date" class="form-control" placeholder="{{ translate('Date of birth') }}" name="dob" value="{{ $date }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mb-0 text-right">
            <button type="submit" class="btn btn-primary">{{ translate('Update Profile') }}</button>
        </div>
    </form>
@endsection
