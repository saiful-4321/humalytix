@extends("Main::layouts.app")


@section('content')
<div class="row">
    <div class="col-lg-5 col-md-8 col-sm-12">                        
        <h2>{{ __('Role wise Permissions') }}</h2>
    </div>            
    <div class="col-lg-7 col-md-4 col-sm-12 text-end">
        <ul class="breadcrumb justify-content-end">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}"><i class="fas fa-home"></i></a></li>                            
            <li class="breadcrumb-item">Role & Permission</li>
            <li class="breadcrumb-item active">Role wise Permissions</li>
        </ul>
    </div>

    <div class="col-lg-12 col-md-12">
        <div class="card bg-white"> 
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="font-weight-medium mb-0">Role wise Permission List</h6>
                    <div class="btn-group">
                        @can('role-list')
                        <a href="{{ route('dashboard.role') }}" class="btn btn-info d-flex align-items-center font-weight-medium">
                            <i class="icon-list"></i> &nbsp;Role List
                        </a>
                        @endcan
                    </div>
                </div>
            </div>

            @include('Main::widgets.message.sweet-alert')

            <div class="card-body p-3">
                {{ Form::open(['route' => 'dashboard.role.has-permission.update']) }}
                {{ Form::hidden('_method', 'put') }}
                {{ Form::hidden('id', $role->id) }} 
                <div class="input-group p-3 pb-0">
                    <input type="text" name="name" class="form-control rounded-0" placeholder="Role Name" aria-label="Role Name" value="{{ $role->name??old('name') }}" style="border-radius: 10px">
                    <button class="btn btn-outline-primary" type="submit" id="button-addon2"><i class="fa fa-save"></i> Update</button>
                </div> 

                <div class="p-3 rounded-0">
                    <label class="form-check-label" for="check_all">
                        <input class="form-check-input" type="checkbox" id="check_all" value="">
                        Check All
                    </label>
                </div>  

                <div class="row">
                    @foreach($modules as $module => $permissions)
                    <div class="col-sm-4">
                        <div class="card checkboxGroup mx-3 ">
                            <div class="card-header"> 
                                <label class="form-check-label">
                                    <input class="form-check-input checkboxHeader" type="checkbox" name="checkboxHeader"> {{ $module??'' }}
                                </label>
                            </div>
                            <div class="card-body">
                                <ul class="list-inline">
                                    @foreach($permissions as $permission)
                                    <li>
                                        <label class="form-check-label px-3 pt-1" for="checkbox_{{$permission->id}}">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" id="checkbox_{{$permission->id}}" value="{{ $permission->name }}" {{ $permission->checked }}>
                                            {{ $permission->name }}
                                        </label>
                                    </li>
                                    @endforeach 
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach 
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#check_all").on('change', function(){ 
        $("input:checkbox").prop('checked', $(this).prop("checked"));
    });

    $('input:checkbox').on('change', function() {

        if($(this).prop("checked") == false) { 
            $("#check_all").prop('checked', false);
        } 

        if ($(this).hasClass('checkboxHeader') && $(this).prop("checked") == true) {
            $(this).closest('.checkboxGroup').find("input:checkbox").prop('checked', true);
        } else if ($(this).hasClass('checkboxHeader') && $(this).prop("checked") == false) {
            $(this).closest('.checkboxGroup').find("input:checkbox").prop('checked', false);
        }   

        $('.checkboxGroup').each(function(){
            if ($(this).find('input:checkbox:checked').length == $(this).find('input:checkbox').length-1) {
                $(this).find('input.checkboxHeader:checkbox').prop('checked', !$(this).find('input.checkboxHeader:checkbox').prop('checked'));
            } 
        });

        if ($('input:checkbox:checked').length == $('input:checkbox').length-1) {
            $("#check_all").prop('checked', true);
        }

    });

    // initial selection
    $('.checkboxGroup').each(function(){
        if ($(this).find('input:checkbox:checked').length == $(this).find('input:checkbox').length-1) {
            $(this).find('input:checkbox').prop('checked', true);
        }
    });

    if ($('input:checkbox:checked').length == $('input:checkbox').length-1) {
        $("#check_all").prop('checked', true);
    }
})
</script>
@endsection