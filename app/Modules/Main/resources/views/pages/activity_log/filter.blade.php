<div class="offcanvas offcanvas-end" tabindex="-1" id="activityLogFilter" aria-labelledby="activityLogFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="activityLogFilterLabel">Filter Activity Logs</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {!! Form::model(request()->all(), ['method' => 'get']) !!}
            <div class="mb-3">
                {!! Form::label('date_from', 'Date From', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-calendar-clock text-primary"></i></span>
                    {{ Form::text('date_from', null, ['class' => 'form-control border-start-0 datepicker', 'id' => 'date_from', 'placeholder' => 'Select Start Date', 'autocomplete' => 'off']) }}
                </div>
            </div>
            
            <div class="mb-3">
                {!! Form::label('to_date', 'Date To', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-calendar-clock text-primary"></i></span>
                    {{ Form::text('to_date', null, ['class' => 'form-control border-start-0 datepicker', 'id' => 'to_date', 'placeholder' => 'Select End Date', 'autocomplete' => 'off']) }}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('causer_id', 'Action By', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-account-circle-outline text-primary"></i></span>
                    {{ Form::select('causer_id', $users, request('causer_id'), ['id' => 'causer_id', 'class' => 'select2 form-control border-start-0', 'placeholder' => 'Select User']) }}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('log_name', 'Log Type', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-format-list-bulleted-type text-primary"></i></span>
                    {{ Form::select('log_name', $logNames ?? [], request('log_name'), ['id' => 'log_name', 'class' => 'select2 form-control border-start-0', 'placeholder' => 'Select Log Type']) }}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('subject_type', 'Subject (Model)', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-database-outline text-primary"></i></span>
                    {{ Form::text('subject_type', request('subject_type'), ['id' => 'subject_type', 'class' => 'form-control border-start-0', 'placeholder' => 'e.g. User']) }}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('subject_id', 'Subject ID', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-identifier text-primary"></i></span>
                    {{ Form::text('subject_id', request('subject_id'), ['id' => 'subject_id', 'class' => 'form-control border-start-0', 'placeholder' => 'e.g. 123']) }}
                </div>
            </div>
            
            <div class="mb-3">
                {!! Form::label('description', 'Description', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-text-box-search-outline text-primary"></i></span>
                    {{ Form::text('description', request('description'), ['id' => 'description', 'class' => 'form-control border-start-0', 'placeholder' => 'Search description...']) }}
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                {!! Form::button('<i class="mdi mdi-filter-outline me-1"></i> Apply Filters', ['type' => 'submit', 'name' => '_filter', 'class' => 'btn btn-primary waves-effect waves-light']) !!}
                <a href="{{ url()->current() }}" class="btn btn-light waves-effect">Reset Filters</a>
            </div>
        {!! Form::close() !!}
    </div>
</div>
