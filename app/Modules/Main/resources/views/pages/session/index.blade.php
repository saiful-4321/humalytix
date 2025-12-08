@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>User Session </h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.user') }}">User Management</a></li>
                <li class="breadcrumb-item active">User Session</li>
            </ul>
        </div>
    </div>
</div>


@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12 col-md-12">
        <div class="card bg-white">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between py-1">
                    <h6 class="font-weight-medium mb-0">User Session - (showing {{ $result->firstItem()??0 }} to {{ $result->lastItem()??0 }} of total {{ $result->total()??0 }} entries)</h6>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sessionFilter" aria-controls="sessionFilter">
                        <i class="mdi mdi-filter-variant me-1"></i> Filter Sessions
                    </button>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['user_id', 'is_pretend', 'start_date', 'end_date']))
                <x-Main::active-filters :url="route('dashboard.user.session')">
                    <x-Main::active-filter-item key="user_id" label="User" :value="$userDropdown[request('user_id')] ?? request('user_id')" />
                    <x-Main::active-filter-item key="is_pretend" label="Is Pretend" :value="request('is_pretend')" />
                    <x-Main::active-filter-item key="start_date" label="Start Date" :value="request('start_date')" />
                    <x-Main::active-filter-item key="end_date" label="End Date" :value="request('end_date')" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr> 
                                <th class="border-top-0">Email</th>
                                <th class="border-top-0">Guard</th>
                                <th class="border-top-0">IP</th>
                                <th class="border-top-0">Agent</th>
                                <th class="border-top-0">Logged in</th>
                                <th class="border-top-0">Logged Out</th>
                                <th class="border-top-0">Is Pretend</th>
                                <th class="border-top-0">Pretend User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($result))
                            @foreach($result as $item) 
                            <tr>
                                <td> {{ $item->user->email ?? '' }} </td>
                                <td> {{ $item->guard }} </td>
                                <td> {{ $item->ip_address }} </td>
                                <td> {{ $item->user_agent }} </td>
                                <td> {{ dbToDateTime($item->logged_in_at) }} </td>
                                <td> {{ dbToDateTime($item->logged_out_at) }} </td>
                                <td>
                                    <span class="badge {{ $item->is_pretend ==  'No' ? 'badge-soft-success' : 'badge-soft-danger' }}"> {{ $item->is_pretend }} </span>
                                </td>
                                <td>
                                    @if($item->is_pretend)
                                    {{ $item->pretend_user->email ?? '' }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif 
                        </tbody>
                    </table>
                </div>
            </div>

            @if (!empty($result->count()))
                <div class="d-flex justify-content-end mt-3 mb-0 px-3">
                    {!! $result->appends(request()->all())->links() !!}
                </div>
            @endif 
        </div>
    </div>
</div>

@include("Main::pages.session.filter")
@endsection


@push("scripts")
<script>
    function passwordResetConfirm(input) {
        Swal.fire({
            title: 'Are you sure ?',
            text: "Would you like to act as this user?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I want!',
            reverseButtons:true
        }).then((result) => {
            if (result.value) {
                window.location.href=input.getAttribute("route");
            } else {
                event.preventDefault();
                return;
            }
        })
    }
</script>
@endpush