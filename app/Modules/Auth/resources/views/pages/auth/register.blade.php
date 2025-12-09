@extends('Auth::layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h3 class="fw-bold">Create Account</h3>
        <p class="text-muted">Get your free {{ config('common.cms.short_title') }} account now.</p>
    </div>

    @include('Main::widgets.message.alert')

    {{ Form::model(request(), ['url' => route('register.save'), 'class' => 'mt-4', 'method'=>'post', 'novalidate']) }}
        
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Enter your full name', 'required']) !!}
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            {!! Form::email('email', null, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Enter your email', 'required']) !!}
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile Number</label>
            {!! Form::text('mobile', null, ['class' => 'form-control' . ($errors->has('mobile') ? ' is-invalid' : ''), 'placeholder' => 'Enter your mobile number', 'required']) !!}
            @error('mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-4">
             <div class="form-check">
                {{ Form::checkbox("agree", 1, false, ["id" => "agree", "class" => "form-check-input"])}} 
                <label for="agree" class="form-check-label">
                    By registering you agree to the {{ config('common.cms.short_title') }} <a href="{{ route('terms-conditions') }}" target="_blank" class="text-primary fw-bold">Terms & Conditions</a>
                </label>
                @error('agree')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
             </div>
        </div>
        
        <div class="mb-3 d-grid">
            {!! Form::submit('Signup', ['class' => 'btn btn-primary waves-effect waves-light']) !!}
        </div>
    {{ Form::close() }}

    <div class="mt-4 text-center">
        <p class="text-muted mb-0">Already have an account ? <a href="{{ route("login") }}" class="fw-bold text-primary"> Login </a> </p>
    </div>
@endsection