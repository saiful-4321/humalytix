@extends('Main::widgets.modal.ajaxify-modal', ['data' => $data ?? ''])

@section('form') 
<div class="row clearfix"> 

    {{ Form::hidden("id", request()->id)}}

    <div class="col-md-12 mb-2">
        {{ Form::label('password', 'New Password', ['class' => 'required']) }}
        <div class="form-group">
            {{ Form::password('password', ['type' => 'password', 'class' => 'form-control', 'maxlength' => 64, 'placeholder' => '********']) }}
        </div>
        @error("password")
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    <div class="col-md-12 mb-2">
        {{ Form::label('password', 'Confirm Password', ['class' => 'required']) }}
        <div class="form-group">
            {{ Form::password('password_confirmation', ['type' => 'password', 'class' => 'form-control', 'maxlength' => 64, 'placeholder' => '********']) }}
        </div>
        @error("password_confirmation")
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div> 
</div>
@endsection

