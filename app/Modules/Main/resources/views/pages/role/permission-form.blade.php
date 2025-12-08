@extends('Main::widgets.modal.ajaxify-modal', ['data' => $data ?? ''])

@section('form') 
<div class="form-group">
    <label class="control-label">Name <span class="text-danger">*</span></label>
    {{ Form::text('name', $data->item->name??old('name'), ['class' => 'form-control', 'placeholder'=>'Enter Module Name']) }}
</div> 

<div class="form-group">
    <label class="control-label">Module Name <span class="text-danger">*</span></label>
    {{ Form::select('module_id', ($data->modules??[]), $data->item->module_id??old('module_id'), ['class'=>'form-control select2', 'placeholder'=>'Select Module']) }}
</div>
@endsection
