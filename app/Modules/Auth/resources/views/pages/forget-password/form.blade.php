@extends('Auth::layouts.auth')

@section('content')
    <div class="text-center">
        <h2 class="mb-0">{{ $pageName ?? null }}</h2>
        <p class="text-muted mt-2">
            @include('Main::widgets.message.alert')
        </p>
    </div>

    {{ Form::model(null, ['url' => route('forgot-password.otp'), 'class' => 'mt-4 pt-2', 'method'=>'get']) }}
        <div class="mb-3">
            {!! Form::label('email', 'Email Address', ['class' => 'form-label']) !!}
            {!! Form::text('email', null, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'id' => 'email', 'placeholder' => 'Enter Email Address']) !!}
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-info w-100 waves-effect waves-light" type="submit">Verify</button>
        </div>
    </form>
 
    <div class="mt-5 text-center">
        <p class="text-muted mb-0">Already have an account ? <a href="{{ route("login") }}" class="text-info fw-semibold"> Login </a> </p>
    </div>
@endsection
