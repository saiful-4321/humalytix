@extends('Main::widgets.modal.ajaxify-modal', ['data' => $data ?? ''])

@section('form') 
<div class="form-group">
    <label class="control-label">Name <span class="text-danger">*</span></label>
    {{ Form::text('name', $data->item->name??old('name'), ['class' => 'form-control', 'placeholder'=>'Enter Module Name']) }}
</div> 

@if(!empty($data->item))
<div class="form-group">
    <label class="control-label">Status <span class="text-danger">*</span></label>
    {{ Form::select('status', ['1'=>'Active', '0'=>'Inactive'], $data->item->status??old('status'), ['class'=>'form-control select2', 'aria-label'=>'Status']) }}
</div>
@endif
@endsection
