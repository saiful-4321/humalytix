@extends("HRM::layouts.settings")

@section("title", "General Settings")
@section("breadcrumb")
    <li class="breadcrumb-item active">Leave Settings</li>
    <li class="breadcrumb-item active">General</li>
@endsection

@section("settings-content")


@include("Main::widgets.message.sweet-alert")

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h6 class="card-title mb-0">System Configuration</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('hrm.settings.general.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-3">Weekly Holidays</label>
                        <div class="row">
                            @php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            @endphp
                            
                            @foreach($days as $day)
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="weekly_holidays[]" value="{{ $day }}" id="day_{{ $day }}" 
                                        {{ in_array($day, $weeklyHolidays) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="day_{{ $day }}">
                                        {{ $day }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-text">Selected days will be marked as "Weekly Off" in the Leave Calendar.</div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
