@extends('Main::widgets.modal.ajaxify-modal', ['data' => $data ?? ''])
  
@section('form') 
<div class="row clearfix">
    <div class="col-md-6 mb-2">
        {{ Form::label('name', 'Name', ['class' => 'required']) }}
        <div class="form-group">
            {{ Form::text('name', ($data->item->name ?? old('name')), ['class' => 'form-control', 'maxlength' => 128, 'placeholder' => 'Enter Name']) }}
        </div>
    </div>


    <div class="col-md-6 mb-2">
        {{ Form::label('email', 'Email Address', ['class' => 'required']) }}
        <div class="form-group">
            @if (!empty($data->item->id))
            {{ Form::text('email', ($data->item->email ?? old('email')), ['class' => 'form-control', 'maxlength' => 128, 'placeholder' => 'Enter Email', 'readonly'=>true]) }}
            @else
            {{ Form::text('email', ($data->item->email ?? old('email')), ['class' => 'form-control', 'maxlength' => 128, 'placeholder' => 'Enter Email']) }}
            @endif
        </div>
    </div>
 

    @if (empty($data->item->password))
    <div class="col-md-6 mb-2">
        {{ Form::label('password', 'Password', ['class' => '']) }}
        <div class="form-group">
            {{ Form::password('password', ['type' => 'password', 'class' => 'form-control', 'maxlength' => 64, 'placeholder' => '********']) }}
        </div>
    </div>
    <div class="col-md-6 mb-2">
        {{ Form::label('password', 'Confirm Password', ['class' => '']) }}
        <div class="form-group">
            {{ Form::password('password_confirmation', ['type' => 'password', 'class' => 'form-control', 'maxlength' => 64, 'placeholder' => '********']) }}
        </div>
    </div>
    @endif

    
    <div class="col-md-6 mb-2">
        {{ Form::label('mobile', 'Mobile Number', ['class' => 'required']) }}
        <div class="form-group">
            {{ Form::text('mobile', ($data->item->mobile ?? old('mobile')), ['class' => 'form-control', 'placeholder' => 'Enter Mobile Number']) }}
        </div>
    </div>    
    <div class="col-md-6 mb-2">
        {{ Form::label('nid', 'NID', ['class' => '']) }}
        <div class="form-group">
            {{ Form::text('nid', ($data->item->nid ?? old('nid')), ['class' => 'form-control', 'placeholder' => 'Enter National Identity Card Number']) }}
        </div>
    </div>

    <div class="col-md-6 mb-2">
        {{ Form::label('date_of_birth', 'Date of Birth', ['class' => '']) }}
        <div class="form-group">
            {{ Form::text('date_of_birth', ($data->item->date_of_birth ?? old('date_of_birth')), ['class' => 'form-control birthdate', 'maxlength' => 64, 'placeholder' => 'Enter Date of Birth', 'autocomplete'=>'off']) }}
        </div>
    </div>
    <div class="col-md-6 mb-2">
        {{ Form::label('roles', 'Roles') }}
        <div class="form-group">
            @php
                $existsRole = !empty($data->item) ? $data->item->getRoleNames() : "";
            @endphp
            {{ Form::select('roles', ($data->roles ?? []), ($existsRole ?? old('roles')), ['class' => 'form-control select2', 'placeholder' => 'Select Roles']) }}
        </div>
    </div> 

    {{-- EDIT MODE --}}
    @if (!empty($data->item))

        {{ Form::hidden('id', $data->item->id) }}  
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('status', 'Status', ['class' => 'required']) !!}
                {!! Form::select('status', ($data->status ?? []), ($data->item->status ?? old('status')), ['class' => 'form-control select2', 'id' => 'status', 'placeholder' => 'Select']) !!}
            </div>
        </div> 

        <div class="col-md-6  mt-3">
            <div class="form-group"> 
                <div class="form-check-inline">
                    <div class="fancy-checkbox">
                        <label>
                            {{ Form::checkbox('refresh_token', 1, false) }} 
                            <span>{{ __('Refresh API Token') }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- reload scripts for dynamic content  --}}
<script src="{{ asset('assets/js/common.js') }}"></script>

@endsection
