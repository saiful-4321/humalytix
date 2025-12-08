<div class="offcanvas offcanvas-end" tabindex="-1" id="userWhitelistedIpFilter" aria-labelledby="userWhitelistedIpFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="userWhitelistedIpFilterLabel">Filter Whitelisted IPs</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        {!! Form::model(request()->all(), ['method' => 'get']) !!}
            <div class="mb-3">
                {!! Form::label('user_id', 'User', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-account-circle-outline text-primary"></i></span>
                    {!! Form::select('user_id', $users, null, ['class' => 'form-control select2 border-start-0', 'id' => 'user_id', 'placeholder' => 'Select User']) !!}
                </div>
            </div>

            <div class="mb-3">
                {!! Form::label('ip_address', 'IP Address', ['class' => 'form-label text-muted font-size-13']) !!}
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-ip-network-outline text-primary"></i></span>
                    {!! Form::text('ip_address', null, ['class' => 'form-control border-start-0', 'id' => 'ip_address', 'placeholder' => 'eg:- 192.168.0.1']) !!}
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