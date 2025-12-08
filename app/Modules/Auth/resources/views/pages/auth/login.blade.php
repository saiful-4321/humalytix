@inject('companyService', 'App\Modules\Settings\Services\CompanyService')
@php
    $company = $companyService->get();
@endphp
@extends('Auth::layouts.auth')

@section('content')
    <div class="text-center">
        @if($company['logo_dark'])
            <img src="{{ asset($company['logo_dark']) }}" alt="" height="30" class="auth-logo-dark mx-auto">
            <img src="{{ asset($company['logo_white']) }}" alt="" height="30" class="auth-logo-light mx-auto">
        @else
            <h2 class="mb-0" style="font-weight: 700;">Welcome Back!</h2>
        @endif
        <p class="text-muted mt-2">Sign in to continue to {{ $company['short_name'] ?? config('common.cms.short_title') }}.</p>
        <div class="mt-2"> 
            @include('Main::widgets.message.alert')
        </div>
    </div>

    {{ Form::model(null, ['url' => route('login.auth'), 'class' => 'mt-4 pt-2', 'method'=>'post']) }}
        
        <div class="custom-input-group">
            <i class="mdi mdi-email-outline"></i>
            {!! Form::text('email', old('email'), ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'id' => 'email', 'placeholder' => 'Email Address']) !!}
            @error('email')
                <div class="invalid-feedback d-block text-start ps-3">{{ $message }}</div>
            @enderror
        </div>

        <div class="custom-input-group mb-3">
            <i class="mdi mdi-lock-outline"></i>
            {{ Form::password('password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'id' => 'password', "placeholder" => "Password"]) }}
            @error('password')
                <div class="invalid-feedback d-block text-start ps-3">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-check" style="background-color: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.5);">
                <label class="form-check-label" for="remember-check" style="font-size: 0.9rem;">Remember me</label>
            </div>
            <div>
                @if($company['password_reset_active'] ?? true)
                <a href="{{ route('forgot-password') }}" class="text-muted" style="font-size: 0.9rem; text-decoration: underline;">Forgot password?</a>
                @endif
            </div>
        </div>
        
        <div class="mb-3">
            <button class="btn-glass waves-effect waves-light" type="submit">Log In</button>
        </div>
    </form>
 
    <div class="mt-4 text-center">
        @if($company['registration_active'] ?? true)
        <p class="text-muted mb-0">Don't have an account ? <a href="{{ route('register') }}" class="fw-bold text-white"> Signup Now </a>
        </p>
        @endif
    </div>
@endsection
