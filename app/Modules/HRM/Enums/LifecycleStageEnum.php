<?php

namespace App\Modules\HRM\Enums;

enum LifecycleStageEnum: string
{
    case HIRING = 'hiring';
    case ONBOARDING = 'onboarding';
    case PROBATION = 'probation';
    case CONFIRMED = 'confirmed';
    case TRANSFER = 'transfer';
    case PROMOTION = 'promotion';
    case RESIGNATION = 'resignation';
    case TERMINATED = 'terminated';

    public function label(): string
    {
        return match($this) {
            self::HIRING => 'Hiring',
            self::ONBOARDING => 'Onboarding',
            self::PROBATION => 'Probation',
            self::CONFIRMED => 'Confirmed',
            self::TRANSFER => 'Transfer',
            self::PROMOTION => 'Promotion',
            self::RESIGNATION => 'Resignation',
            self::TERMINATED => 'Terminated',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::HIRING => 'info',
            self::ONBOARDING => 'primary',
            self::PROBATION => 'warning',
            self::CONFIRMED => 'success',
            self::TRANSFER => 'info',
            self::PROMOTION => 'success',
            self::RESIGNATION => 'secondary',
            self::TERMINATED => 'danger',
        };
    }
}
