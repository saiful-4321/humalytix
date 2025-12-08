@extends('Auth::layouts.auth')

@section('content')
    <div class="text-center">
        <h2 class="mb-0" style="font-weight: 700;">Create Account</h2>
        <p class="text-muted mt-2">Get your free {{ config('common.cms.short_title') }} account now.</p>
        <div class="mt-2"> 
            @include('Main::widgets.message.alert')
        </div>
    </div>
    {{ Form::model(request(), ['url' => route('register.save'), 'class' => 'mt-4 pt-2', 'method'=>'post', 'novalidate']) }}
        
        <div class="custom-input-group">
            <i class="mdi mdi-account-outline"></i>
            {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''), 'placeholder' => 'Full Name', 'required']) !!}
            @error('name')
                <div class="validation-error text-start ps-3">{{ $message }}</div>
            @enderror
        </div>

        <div class="custom-input-group">
            <i class="mdi mdi-email-outline"></i>
            {!! Form::email('email', null, ['class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''), 'placeholder' => 'Email Address', 'required']) !!}
            @error('email')
                <div class="validation-error text-start ps-3">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="custom-input-group">
            <i class="mdi mdi-phone-outline"></i>
            {!! Form::text('mobile', null, ['class' => 'form-control' . ($errors->has('mobile') ? ' is-invalid' : ''), 'placeholder' => 'Mobile Number', 'required']) !!}
            @error('mobile')
                <div class="validation-error text-start ps-3">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-4">
            <p class="mb-0">
                <label for="agree" class="mb-0" style="font-size: 0.9rem;">
                    {{ Form::checkbox("agree", 1, false, ["id" => "agree", "class" => "form-check-input", "style" => "background-color: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.5);"])}} 
                    By registering you agree to the {{ config('common.cms.short_title') }} <a href="{{ route('terms-conditions') }}" target="_blank" class="text-white fw-bold" title="Terms & Conditions">Terms & Conditions</a>
                </label>
                @error('agree')
                    <div class="validation-error mt-0 text-start ps-3">{{ $message }}</div>
                @enderror
            </p>
        </div>
        
        <div class="mb-3">
            {!! Form::submit('Signup', ['class' => 'btn-glass waves-effect waves-light']) !!}
        </div>
    {{ Form::close() }}

    <div class="mt-4 text-center">
        <p class="text-muted mb-0">Already have an account ? <a href="{{ route("login") }}" class="fw-bold text-white"> Login </a> </p>
    </div>
@endsection


@push("styles")
<!-- flatpickr  -->
<link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
@endpush

@push("scripts") 
{{-- moment js --}}
<script src="{{ asset('assets/libs/momentjs/momentjs.min.js') }}"></script> 
<!-- flatpickr  -->
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script> 
<script>
$(document).ready(function() {
    // datepicker
    $('.birthdate').flatpickr({
        altInput: true,
        dateFormat: "YYYY-MM-DD",
        altFormat: "YYYY-MM-DD",
        maxDate: "today", 
        allowInput: true,
        parseDate: (datestr, format) => {
            return moment(datestr, format, true).toDate();
        },
        formatDate: (date, format, locale) => {
            return moment(date).format(format);
        }
    });
})
</script>
@endpush