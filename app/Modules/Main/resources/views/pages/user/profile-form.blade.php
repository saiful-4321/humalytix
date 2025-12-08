@extends('Main::widgets.modal.ajaxify-modal', ['data' => $data ?? ''])

@section('form') 
<div class="row clearfix">

    {{ Form::hidden('id', auth()->user()->id) }}

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
    <div class="col-md-6 mb-2">
        {{ Form::label('mobile', 'Mobile Number', ['class' => 'required']) }}
        <div class="form-group">
            {{ Form::text('mobile', ($data->item->mobile ?? old('mobile')), ['class' => 'form-control', 'placeholder' => 'Enter Mobile Number']) }}
        </div>
    </div>    
    <div class="col-md-6 mb-2">
        {{ Form::label('nid', 'NID', ['class' => 'required']) }}
        <div class="form-group">
            {{ Form::text('nid', ($data->item->nid ?? old('nid')), ['class' => 'form-control', 'placeholder' => 'Enter National Identity Card Number']) }}
        </div>
    </div>
    <div class="col-md-6 mb-2">
        {{ Form::label('date_of_birth', 'Date of Birth', ['class' => 'required']) }}
        <div class="form-group">
            {{ Form::text('date_of_birth', ($data->item->date_of_birth ?? old('date_of_birth')), ['class' => 'form-control datepicker', 'maxlength' => 64, 'placeholder' => 'Enter Date of Birth', 'autocomplete'=>'off']) }}
        </div>
    </div> 
</div>

{{-- reload scripts for dynamic content  --}}
<script src="{{ asset('assets/js/common.js') }}"></script>
@endsection
