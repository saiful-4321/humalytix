@extends('Main::widgets.modal.ajaxify-modal', ['data' => $data ?? ''])

@section('form') 
<div class="row clearfix">

    {{-- INSERT MODE  --}}
    @if (empty($data->item->id)) 
        <div class="col-md-12">
            <div class="form-group">
                <label>Users</label>
                {{Form::select('user_id', ($data->users ?? []), ($data->item->user_id ?? old('user_id')), ['class' => 'form-control select2', "placeholder"=>"Select"])}}
            </div>
        </div>                        
        <div class="col-md-12">
            <div class="form-group">
                <label>IP Address</label>
                {{Form::text('ip_address', ($data->item->ip_address ?? old('ip_address')), ['class' => 'form-control', "placeholder"=>"eg:- 192.168.0.1"])}}
            </div>
        </div> 
    @endif

    {{-- EDIT MODE --}}
    @if (!empty($data->item))
        {{ Form::hidden('id', $data->item->id) }} 

        <div class="col-md-12">
            <div class="form-group">
                <label>User</label>
                {{Form::text('', ($data->item->name ?? old('name')), ['class' => 'form-control', "placeholder" => "User Name", "readonly"])}}
            </div>
        </div>             
        <div class="col-md-12">
            <div class="form-group">
                <label>IP Address</label>
                {{Form::text('', ($data->item->ip_address ?? old('ip_address')), ['class' => 'form-control', "placeholder"=>"eg:- 192.168.0.1", "readonly"])}}
            </div>
        </div> 

        <div class="col-12">
            <div class="">
                <label class="fancy-radio">
                    <input name="status" type="radio" value="1" {{ ( ($data->item->status ?? old('status')) == "1") ? 'checked' : '' }}>
                    <span><i></i>Active</span>
                </label>
                <label class="fancy-radio">
                    <input name="status" type="radio" value="0" {{ ( ($data->item->status ?? old('status')) == "0") ? 'checked' : '' }}>
                    <span><i></i>Inactive</span>
                </label>
            </div>
        </div>
    @endif
</div>


<script>
$(document).ready(function() {   
    // select2
    $('.select2').select2();
});
</script>
@endsection

