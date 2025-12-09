<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HRM Module Configuration
    |--------------------------------------------------------------------------
    */

    'employee_code_prefix' => 'EMP',
    'employee_code_length' => 6,
    
    'candidate_code_prefix' => 'CAN',
    
    'job_code_prefix' => 'JOB',
    
    // Attendance settings
    'grace_period_minutes' => 15,
    'half_day_hours' => 4,
    'full_day_hours' => 8,
    
    // Geofencing
    'default_geofence_radius' => 100, // meters
    
    // Probation period
    'default_probation_days' => 90,
    
    // Notice period
    'default_notice_period_days' => 30,
    
    // Document upload
    'max_document_size' => 5120, // KB (5MB)
    'allowed_document_types' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
    
    // Time tracking
    'timesheet_period' => 'weekly', // weekly, monthly
    
    // Biometric sync
    'biometric_sync_interval' => 15, // minutes
];
