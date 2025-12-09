<?php

if (!function_exists('generate_employee_code')) {
    /**
     * Generate unique employee code
     */
    function generate_employee_code(): string
    {
        $prefix = config('hrm.employee_code_prefix', 'EMP');
        $length = config('hrm.employee_code_length', 6);
        
        do {
            $number = str_pad(rand(1, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
            $code = $prefix . $number;
            $exists = \App\Modules\HRM\Models\Employee::where('employee_code', $code)->exists();
        } while ($exists);
        
        return $code;
    }
}

if (!function_exists('generate_candidate_code')) {
    /**
     * Generate unique candidate code
     */
    function generate_candidate_code(): string
    {
        $prefix = config('hrm.candidate_code_prefix', 'CAN');
        $year = date('Y');
        
        $lastCandidate = \App\Modules\HRM\Models\Candidate::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastCandidate ? (int)substr($lastCandidate->candidate_code, -4) + 1 : 1;
        
        return $prefix . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_job_code')) {
    /**
     * Generate unique job code
     */
    function generate_job_code(): string
    {
        $prefix = config('hrm.job_code_prefix', 'JOB');
        $year = date('Y');
        
        $lastJob = \App\Modules\HRM\Models\Job::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastJob ? (int)substr($lastJob->code, -4) + 1 : 1;
        
        return $prefix . $year . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('calculate_working_hours')) {
    /**
     * Calculate working hours between two times
     */
    function calculate_working_hours(?string $startTime, ?string $endTime, float $breakHours = 0): ?float
    {
        if (!$startTime || !$endTime) {
            return null;
        }
        
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($endTime);
        
        $totalHours = $end->diffInMinutes($start) / 60;
        $workingHours = $totalHours - $breakHours;
        
        return round(max(0, $workingHours), 2);
    }
}

if (!function_exists('validate_geofence')) {
    /**
     * Validate if coordinates are within geofence
     */
    function validate_geofence(float $lat1, float $lon1, float $lat2, float $lon2, int $radius): bool
    {
        $earthRadius = 6371000; // meters
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return $distance <= $radius;
    }
}

if (!function_exists('get_attendance_status')) {
    /**
     * Determine attendance status based on check-in time and shift
     */
    function get_attendance_status(?string $checkInTime, ?string $shiftStartTime, int $gracePeriod = 15): string
    {
        if (!$checkInTime) {
            return 'absent';
        }
        
        if (!$shiftStartTime) {
            return 'present';
        }
        
        $checkIn = \Carbon\Carbon::parse($checkInTime);
        $shiftStart = \Carbon\Carbon::parse($shiftStartTime);
        $graceTime = $shiftStart->copy()->addMinutes($gracePeriod);
        
        if ($checkIn->lte($graceTime)) {
            return 'present';
        }
        
        return 'late';
    }
}

if (!function_exists('format_duration')) {
    /**
     * Format duration in hours to human readable format
     */
    function format_duration(?float $hours): string
    {
        if (!$hours) {
            return '0h 0m';
        }
        
        $h = floor($hours);
        $m = round(($hours - $h) * 60);
        
        return "{$h}h {$m}m";
    }
}

if (!function_exists('get_lifecycle_stage_color')) {
    /**
     * Get color class for lifecycle stage
     */
    function get_lifecycle_stage_color(string $stage): string
    {
        return \App\Modules\HRM\Enums\LifecycleStageEnum::from($stage)->color();
    }
}

if (!function_exists('get_candidate_stage_color')) {
    /**
     * Get color class for candidate stage
     */
    function get_candidate_stage_color(string $stage): string
    {
        return \App\Modules\HRM\Enums\CandidateStageEnum::from($stage)->color();
    }
}
