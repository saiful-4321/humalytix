<?php

namespace App\Modules\HRM\Enums;

enum AttendanceStatusEnum: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case HALF_DAY = 'half_day';
    case LATE = 'late';
    case ON_LEAVE = 'on_leave';
    case HOLIDAY = 'holiday';
    case WEEKEND = 'weekend';

    public function label(): string
    {
        return match($this) {
            self::PRESENT => 'Present',
            self::ABSENT => 'Absent',
            self::HALF_DAY => 'Half Day',
            self::LATE => 'Late',
            self::ON_LEAVE => 'On Leave',
            self::HOLIDAY => 'Holiday',
            self::WEEKEND => 'Weekend',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PRESENT => 'success',
            self::ABSENT => 'danger',
            self::HALF_DAY => 'warning',
            self::LATE => 'warning',
            self::ON_LEAVE => 'info',
            self::HOLIDAY => 'primary',
            self::WEEKEND => 'secondary',
        };
    }
}
