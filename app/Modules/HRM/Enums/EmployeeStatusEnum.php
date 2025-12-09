<?php

namespace App\Modules\HRM\Enums;

enum EmployeeStatusEnum: string
{
    case ACTIVE = 'active';
    case PROBATION = 'probation';
    case CONFIRMED = 'confirmed';
    case NOTICE_PERIOD = 'notice_period';
    case RESIGNED = 'resigned';
    case TERMINATED = 'terminated';
    case SUSPENDED = 'suspended';
    case ON_LEAVE = 'on_leave';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::PROBATION => 'On Probation',
            self::CONFIRMED => 'Confirmed',
            self::NOTICE_PERIOD => 'Notice Period',
            self::RESIGNED => 'Resigned',
            self::TERMINATED => 'Terminated',
            self::SUSPENDED => 'Suspended',
            self::ON_LEAVE => 'On Leave',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::PROBATION => 'warning',
            self::CONFIRMED => 'primary',
            self::NOTICE_PERIOD => 'info',
            self::RESIGNED => 'secondary',
            self::TERMINATED => 'danger',
            self::SUSPENDED => 'dark',
            self::ON_LEAVE => 'info',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
