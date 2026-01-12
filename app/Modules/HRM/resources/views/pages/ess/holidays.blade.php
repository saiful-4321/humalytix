@extends('Main::layouts.app')

@section('title', 'Holiday Calendar | ESS')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Holiday Calendar</h4>
            <div class="page-title-right">
                 <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('hrm.ess.dashboard') }}">ESS</a></li>
                    <li class="breadcrumb-item active">Holidays</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
         <div class="card bg-white">
            <div class="card-header align-items-center d-flex border-bottom-0 rounded-top p-3 shadow-sm">
                <h4 class="card-title mb-0 flex-grow-1 font-weight-bold text-dark">
                    <i class="mdi mdi-calendar-star me-2 text-primary"></i> Calendar
                </h4>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mb-5">
         <div class="card bg-white">
            <div class="card-header align-items-center d-flex border-bottom-0 rounded-top p-3 shadow-sm">
                <h4 class="card-title mb-0 flex-grow-1 font-weight-bold text-dark">
                    <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i> Upcoming Holidays List
                </h4>
            </div>
            <div class="card-body p-0">
                <div class="row pt-3 px-2">
                    @forelse($groupedHolidays as $month => $monthHolidays)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="border rounded h-100">
                                <div class="bg-light p-2 rounded-top border-bottom">
                                    <h5 class="font-size-14 mb-0 fw-bold text-center text-primary">{{ $month }}</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-nowrap align-middle mb-0 table-borderless">
                                        <tbody>
                                            @foreach($monthHolidays as $holiday)
                                            <tr>
                                                <td style="width: 50px;" class="fw-bold ps-3 text-muted">{{ $holiday['date']->format('d') }}</td>
                                                <td style="width: 100px;">{{ $holiday['day'] }}</td>
                                                <td class="text-wrap">{{ $holiday['name'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="mdi mdi-calendar-remove display-4 text-muted d-block mb-3"></i>
                            <p class="text-muted">No upcoming holidays found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- FullCalendar CDN -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: @json($events),
            eventClick: function(info) {
                // Optional: show modal with details
                // alert('Event: ' + info.event.title);
            },
            height: 'auto',
            contentHeight: 650,
            aspectRatio: 2,
        });
        calendar.render();
    });
</script>
<style>
    /* FullCalendar Customization to match theme */
    .fc-toolbar-title { font-size: 1.25rem !important; }
    .fc-button-primary { background-color: #556ee6 !important; border-color: #556ee6 !important; }
    .fc-button-primary:hover { background-color: #485ec4 !important; border-color: #485ec4 !important; }
    .fc-button-active { background-color: #485ec4 !important; border-color: #485ec4 !important; }
    .fc-daygrid-event { font-size: 0.85em; cursor: pointer; }
</style>
@endpush
