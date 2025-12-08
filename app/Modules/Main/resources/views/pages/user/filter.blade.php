<div class="offcanvas offcanvas-end" tabindex="-1" id="userFilter" aria-labelledby="userFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="userFilterLabel">Filter Users</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {!! Form::model(request()->all(), ['method' => 'get']) !!}
            <div class="mb-3">
                {!! Form::label('user_id', 'User', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-account-circle-outline text-primary"></i></span>
                    {!! Form::select('user_id', $userDropdown, null, ['class' => 'form-control select2 border-start-0', 'id' => 'user_id', 'placeholder' => 'Select User']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('name', 'Name', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-account-outline text-primary"></i></span>
                    {!! Form::text('name', null, ['class' => 'form-control border-start-0', 'id' => 'name', 'placeholder' => 'Enter Name']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('email', 'Email Address', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-email-outline text-primary"></i></span>
                    {!! Form::email('email', null, ['class' => 'form-control border-start-0', 'id' => 'email', 'placeholder' => 'Enter Email']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('mobile', 'Mobile', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-phone-outline text-primary"></i></span>
                    {!! Form::text('mobile', null, ['class' => 'form-control border-start-0', 'id' => 'mobile', 'placeholder' => 'Enter Mobile']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('role', 'Role', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-account-key-outline text-primary"></i></span>
                    {!! Form::select('role', $roles, null, ['class' => 'form-control select2 border-start-0', 'id' => 'role', 'placeholder' => 'Select Role']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('nid', 'NID', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-card-account-details-outline text-primary"></i></span>
                    {!! Form::text('nid', null, ['class' => 'form-control border-start-0', 'id' => 'nid', 'placeholder' => 'Enter NID']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('status', 'Status', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-list-status text-primary"></i></span>
                    {!! Form::select('status', $status, null, ['class' => 'form-control select2 border-start-0', 'id' => 'status', 'placeholder' => 'Select Status']) !!}
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                {!! Form::button('<i class="mdi mdi-filter-outline me-1"></i> Apply Filters', ['type' => 'submit', 'name' => '_filter', 'class' => 'btn btn-primary waves-effect waves-light']) !!}
                <a href="{{ url()->current() }}" class="btn btn-light waves-effect">Reset Filters</a>
            </div>
        {!! Form::close() !!} 
    </div>
</div>
