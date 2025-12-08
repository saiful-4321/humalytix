@extends("Main::layouts.app")

@section("content")
<div class="block-header">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>{{ $pageName ?? null }} - <small>({{ request()->get("status") ? request()->get("status") : "All"  }})</small> </h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb justify-content-end">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.log.activity-log') }}"> Logs</a></li>
                <li class="breadcrumb-item active">{{ $pageName ?? null }}</li>
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
                    <h6 class="font-weight-medium mb-0">{{ $pageName ?? null }} - (showing {{ $result->firstItem()??0 }} to {{ $result->lastItem()??0 }} of total {{ $result->total()??0 }} entries)</h6>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#activityLogFilter" aria-controls="activityLogFilter">
                        <i class="mdi mdi-filter-variant me-1"></i> Filter Logs
                    </button>
                </div>
            </div>

            <!-- Active Filters Section -->
            @if(request()->hasAny(['date_from', 'to_date', 'causer_id', 'log_name', 'subject_type', 'subject_id', 'description']))
                <x-Main::active-filters :url="route('dashboard.log.activity-log')">
                    <x-Main::active-filter-item key="date_from" label="From" :value="request('date_from')" />
                    <x-Main::active-filter-item key="to_date" label="To" :value="request('to_date')" />
                    <x-Main::active-filter-item key="causer_id" label="User" :value="$users[request('causer_id')] ?? request('causer_id')" />
                    <x-Main::active-filter-item key="log_name" label="Type" :value="request('log_name')" />
                    <x-Main::active-filter-item key="subject_type" label="Model" :value="request('subject_type')" />
                    <x-Main::active-filter-item key="subject_id" label="ID" :value="request('subject_id')" />
                    <x-Main::active-filter-item key="description" label="Desc" :value="request('description')" />
                </x-Main::active-filters>
            @endif

            <div class="card-body p-0">
                <div class="table-responsive rounded-10 border">
                    <table class="table table-hover align-middle table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 70px;">#</th>
                                <th scope="col">User & Date</th>
                                <th scope="col">Activity & Subject</th>
                                <th scope="col">Changes & Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($result->count())
                                @php $i = $result->firstItem() ?? 0 @endphp
                                @foreach ($result as $log)
                                    <tr>
                                        <td>
                                            <div class="avatar-xs">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-12">
                                                    {{ $i++ }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <h5 class="font-size-14 mb-1"><a href="javascript: void(0);" class="text-dark">{!! getLogUser($log->causer_type, $log->causer_id) !!}</a></h5>
                                            <p class="text-muted mb-0 font-size-12">
                                                <i class="mdi mdi-clock-outline me-1"></i>{{ $log->created_at->format("d M, Y h:i A") }}
                                            </p>
                                            <div class="mt-1">
                                                {!! getLogProperties($log->properties, "agent") !!}
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = 'bg-soft-primary text-primary';
                                                $icon = 'mdi-circle-edit-outline';
                                                if ($log->log_name == 'CREATED') {
                                                    $badgeClass = 'bg-soft-success text-success';
                                                    $icon = 'mdi-plus-circle-outline';
                                                } elseif ($log->log_name == 'DELETED') {
                                                    $badgeClass = 'bg-soft-danger text-danger';
                                                    $icon = 'mdi-trash-can-outline';
                                                }
                                            @endphp
                                            <div class="d-flex align-items-center flex-column">
                                                <div class="font-size-20 me-2 mt-1">
                                                    <i class="mdi {{ $icon }} {{ str_replace('bg-soft-', 'text-', $badgeClass) }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="mb-1 d-flex flex-column">
                                                        <span class="badge {{ $badgeClass }} font-size-12">{{ $log->log_name }}</span>
                                                        <span class="text-muted font-size-13 ms-1">{{ $log->description }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center text-muted font-size-12">
                                                        <span class="me-1">Subject:</span>
                                                        <span class="fw-bold text-dark">{{ class_basename($log->subject_type) ?? "N/A" }}</span>
                                                        <span class="ms-1">(ID: {{ $log->subject_id ?? "-" }})</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="white-space: normal" class="w-50">
                                            <div class="activity-details">
                                                {!! getLogProperties($log->properties, "data") !!}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center p-4">
                                        <div class="text-center">
                                            <div class="avatar-md mx-auto mb-3">
                                                <div class="avatar-title rounded-circle bg-light">
                                                    <i class="mdi mdi-file-search-outline font-size-24 text-primary"></i>
                                                </div>
                                            </div>
                                            <h5 class="text-muted">No activity logs found</h5>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @if (!empty($result->count()))
                <div class="d-flex justify-content-end mt-3 mb-0">
                    {!! $result->appends(request()->all())->links() !!}
                </div>
            @endif 
        </div>
    </div>
</div>

<!-- Include the Offcanvas Filter -->
@include("Main::pages.activity_log.filter")

@endsection
