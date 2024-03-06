@extends('frontend.layouts.app')

@section('content')
<div class="py-6">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                <div class="bg-white rounded shadow-sm p-4 text-left">
                    <h1 class="h3 fw-600 mb-3">{{ translate('Verify Your Email Address') }}</h1>
                    <p class="opacity-60">
                        {{ translate('Before proceeding, please check your email for a verification code') }}
                        {{ translate('If you did not receive the email.') }}
                    </p>
                    <form action="{{ route('verification.submit') }}">
                        @csrf
                        <input type="hidden" name="credential" value="email">
                        <div class="form-group">
                            <div class="input-group input-group--style-1">
                                <input type="text" class="form-control" name="verification_code">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{ translate('Verify') }}</button>
                    </form>
                    <a href="{{ route('verification.resend') }}" class="btn btn-primary btn-block">{{ translate('Click here to request again') }}</a>
                    @if (session('resent'))
                        <div class="alert alert-success mt-2 mb-0" role="alert">
                            {{ translate('A fresh verification code has been sent to your email address.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
