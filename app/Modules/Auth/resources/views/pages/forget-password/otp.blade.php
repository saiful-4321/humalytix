@extends('Auth::layouts.auth')

@section('content')
    <div class="text-center">
        <h2 class="mb-0">{{ $pageName ?? "OTP Verification" }}</h2>
        <p class="text-muted mt-2">{{ $subTitle ?? null }}</p>
        <p class="py-3">{{ $otp["message"] ?? "" }}</p>
        <p class="text-muted mt-2">
            @include('Main::widgets.message.alert')
        </p>
    </div>

    {{ Form::model(request()->all(), ['url' => route('forgot-password.verify'), 'class' => 'form-auth-small', 'method'=>'get']) }}
        {{ Form::hidden('remember_token', $otp['remember_token'] ?? null) }}
        {{ Form::hidden('mobile', $otp['mobile'] ?? null) }}
        {{ Form::hidden('email', $otp['email'] ?? null) }}
        <div class="mb-5 mt-5">
            <div class="input-group">
                <input autofocus name="otp[]" oninput="autoFillOTP(event, 1)" onpaste="autoPasteOTP(event, 1)" type="text" class="form-control me-2 custom-form-control-opt" value="" maxlength="1" size="1" autocomplete="off">
                <input name="otp[]" oninput="autoFillOTP(event, 2)" onpaste="autoPasteOTP(event, 2)" type="text" class="form-control me-2 custom-form-control-opt" value="" maxlength="1" size="1" autocomplete="off">
                <input name="otp[]" oninput="autoFillOTP(event, 3)" onpaste="autoPasteOTP(event, 3)" type="text" class="form-control me-2 custom-form-control-opt" value="" maxlength="1" size="1" autocomplete="off">
                <input name="otp[]" oninput="autoFillOTP(event, 4)" onpaste="autoPasteOTP(event, 4)" type="text" class="form-control me-2 custom-form-control-opt" value="" maxlength="1" size="1" autocomplete="off">
                <input name="otp[]" oninput="autoFillOTP(event, 5)" onpaste="autoPasteOTP(event, 5)" type="text" class="form-control me-2 custom-form-control-opt" value="" maxlength="1" size="1" autocomplete="off">
                <input name="otp[]" oninput="autoFillOTP(event, 6)" onpaste="autoPasteOTP(event, 6)" type="text" class="form-control custom-form-control-opt" value="" maxlength="1" size="1" autocomplete="off">
            </div>
            @if (session()->has("error"))
                <div class="validation-error">{{ session('error') }}</div>
            @endif
            @error('mobile')
                <div class="validation-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <p class="mb-0">Did't not receive the OTP? <a href="{{ url()->full() }}" id="resendBtn" title="Resend verification code" class="text-info d-inline">Recend OTP</a> </p>
            <p>Resend OTP in <span id="countdown">00:00</span>s</p>
        </div>
        
        <div class="mb-3">
            <button type="submit" class="btn btn-info w-100 waves-effect waves-light" href="dashboard.php">Verify and Proceed</button>
        </div>
    {{ Form::close() }}
@endsection


@push('styles')
<style>
.custom-form-control-opt {
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
    border-top-left-radius: 10px !important;
    border-bottom-left-radius: 10px !important;
    text-align: center;
    font-weight: 700;
} 
</style>
@endpush

@push('scripts')
    <script>
        var countDown = document.querySelector("#countdown");
        var resendBtn = document.querySelector("#resendBtn");

        resendBtn.style.display = "none";
        const expiredAt = new Date('{{ $otp['expired_time'] ?? null }}');
        const nowAt = new Date();
        const differenceInSeconds = Math.floor((expiredAt.getTime() - nowAt.getTime()) / 1000);

        startTimer(differenceInSeconds);

        function startTimer(duration) {
            let timer = duration;
            let minutes, seconds;
            
            if (duration < 0) {
                countDown.innerHTML = "00:00";
                resendBtn.style.display = "block";
                return false;
            }

            let interval = setInterval(function() {
                minutes = Math.floor(timer / 60);
                seconds = timer % 60;

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                countDown.innerHTML = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    resendBtn.style.display = "block";
                }
            }, 1000);
        }

        function autoFillOTP(event, index) {
            const otpInputs    = event.target.parentNode.querySelectorAll('input[name="otp[]"]');
            const currentValue = event.target.value;
            event.target.value = currentValue.slice(0, 1);
            if (index < otpInputs.length) {
                otpInputs[index].focus();
            }
        }

        function autoPasteOTP(event, index) {
            event.preventDefault();
            const otpValue = event.clipboardData.getData('text');
            const otpInputs = event.target.parentNode.querySelectorAll('input[name="otp[]"]');

            otpInputs.forEach(function(input, index) {
                input.value = otpValue.charAt(index) || '';
            });
        }
    </script>
@endpush
