<div class="offcanvas offcanvas-end" tabindex="-1" id="roleFilter" aria-labelledby="roleFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="roleFilterLabel">Filter Roles</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {!! Form::model(request()->all(), ['method' => 'get']) !!}
            <div class="mb-3">
                {!! Form::label('name', 'Role Name', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-account-key-outline text-primary"></i></span>
                    {!! Form::text('name', null, ['class' => 'form-control border-start-0', 'id' => 'name', 'placeholder' => 'Enter Role Name']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('start_date', 'Start Date', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-calendar-clock text-primary"></i></span>
                    {!! Form::text('start_date', null, ['class' => 'form-control border-start-0 datepicker', 'id' => 'start_date', 'placeholder' => 'Select Start Date', 'autocomplete' => 'off']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('end_date', 'End Date', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-calendar-clock text-primary"></i></span>
                    {!! Form::text('end_date', null, ['class' => 'form-control border-start-0 datepicker', 'id' => 'end_date', 'placeholder' => 'Select End Date', 'autocomplete' => 'off']) !!}
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                {!! Form::button('<i class="mdi mdi-filter-outline me-1"></i> Apply Filters', ['type' => 'submit', 'name' => '_filter', 'class' => 'btn btn-primary waves-effect waves-light']) !!}
                <a href="{{ url()->current() }}" class="btn btn-light waves-effect">Reset Filters</a>
            </div>
        {!! Form::close() !!} 
    </div>
</div>
