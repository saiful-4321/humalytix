@extends('Auth::layouts.auth')

@section('content')
    <div class="text-center">
        <h2 class="mb-0">{{ $pageName ?? null }}</h2>
        <p class="text-muted mt-2">{{ $subTitle ?? null }}</p>
        <p class="text-muted mt-2">
            @include('Main::widgets.message.alert')
        </p>
    </div>
    {{ Form::model(request(), ['url' => route('forgot-password.reset'), 'class' => 'mt-4 pt-2', 'method'=>'post', 'novalidate']) }}

        {{ Form::hidden('remember_token', $request->remember_token ?? null) }}
        {{ Form::hidden('mobile', $request->remember_token ?? null) }}
        {{ Form::hidden('otp', $request->otp ?? null) }}

        {!! Form::label('password', 'Password', ['class' => 'form-label required']) !!}
         <div class="input-group auth-pass-inputgroup">
            {{ Form::password('password', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'id' => 'password', "placeholder" => "Enter Password", "id" => "password", "aria-label"=>"Password", "aria-describedby"=>"password-addon"]) }}
                <button class="btn btn-light shadow-none ms-0 secret-password" type="button" id="password-addon2"><i class="mdi mdi-eye-outline"></i></button>
            @error('password')
                <div class="validation-error">{{ $message }}</div>
            @enderror
        </div>

        {!! Form::label('password_confirmation', 'Confirmation Password', ['class' => 'form-label required mt-3']) !!}
        <div class="input-group auth-pass-inputgroup">
            {{ Form::password('password_confirmation', ['class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''), 'id' => 'password', "placeholder" => "Enter Password", "id" => "password_confirmation", "aria-label"=>"Password", "aria-describedby"=>"password-addon"]) }}
                <button class="btn btn-light shadow-none ms-0 secret-password" type="button" id="password-addon3"><i class="mdi mdi-eye-outline"></i></button>
            @error('password_confirmation')
                <div class="validation-error">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mt-3">
            {!! Form::submit('Reset', ['class' => 'btn btn-info w-100 waves-effect waves-light']) !!}
        </div>
    {{ Form::close() }}

    <div class="mt-5 text-center">
        <p class="text-muted mb-0">Already have an account ? <a href="{{ route("login") }}" class="text-info fw-semibold"> Login </a> </p>
    </div>
@endsection
