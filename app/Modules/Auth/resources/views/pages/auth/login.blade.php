@inject('companyService', 'App\Modules\Settings\Services\CompanyService')
@php
    $company = $companyService->get();
@endphp
@extends('Auth::layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h3 class="fw-bold">Welcome Back!</h3>
        <p class="text-muted">Sign in to continue to {{ $company['short_name'] ?? config('common.cms.short_title') }}.</p>
    </div>

    @include('Main::widgets.message.alert')

    {{ Form::model(null, ['url' => route('login.auth'), 'class' => 'mt-4', 'method'=>'post']) }}
        
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            {!! Form::text('email', old('email'), ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'id' => 'email', 'placeholder' => 'Enter your email']) !!}
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="float-end">
                @if($company['password_reset_active'] ?? true)
                <a href="{{ route('forgot-password') }}" class="text-muted">Forgot password?</a>
                @endif
            </div>
            <label class="form-label" for="password">Password</label>
            <div class="password-input-group">
                {{ Form::password('password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'id' => 'password', "placeholder" => "Enter your password"]) }}
                <i class="mdi mdi-eye-outline password-toggle-icon"></i>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="remember-check">
            <label class="form-check-label" for="remember-check">Remember me</label>
        </div>
        
        <div class="mt-3 d-grid">
            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
        </div>
    </form>
 
    <div class="mt-4 text-center">
        @if($company['registration_active'] ?? true)
        <p class="text-muted mb-0">Don't have an account ? <a href="{{ route('register') }}" class="fw-bold text-primary"> Signup Now </a>
        </p>
        @endif
    </div>
@endsection
