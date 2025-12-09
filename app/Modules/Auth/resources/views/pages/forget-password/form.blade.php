@extends('Auth::layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h3 class="fw-bold">{{ $pageName ?? 'Forgot Password' }}</h3>
        <p class="text-muted">Enter your email to receive recovery instructions.</p>
    </div>

    @include('Main::widgets.message.alert')

    {{ Form::model(null, ['url' => route('forgot-password.otp'), 'class' => 'mt-4', 'method'=>'get']) }}
        <div class="mb-3">
            {!! Form::label('email', 'Email Address', ['class' => 'form-label']) !!}
            {!! Form::text('email', null, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'id' => 'email', 'placeholder' => 'Enter Email Address']) !!}
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3 d-grid">
            <button type="submit" class="btn btn-primary waves-effect waves-light" type="submit">Verify</button>
        </div>
    </form>
 
    <div class="mt-4 text-center">
        <p class="text-muted mb-0">Remember It ? <a href="{{ route("login") }}" class="text-primary fw-bold"> Login </a> </p>
    </div>
@endsection
