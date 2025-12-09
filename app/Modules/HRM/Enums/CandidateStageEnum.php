<?php

namespace App\Modules\HRM\Enums;

enum CandidateStageEnum: string
{
    case APPLIED = 'applied';
    case SCREENING = 'screening';
    case INTERVIEW = 'interview';
    case OFFERED = 'offered';
    case HIRED = 'hired';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match($this) {
            self::APPLIED => 'Applied',
            self::SCREENING => 'Screening',
            self::INTERVIEW => 'Interview',
            self::OFFERED => 'Offered',
            self::HIRED => 'Hired',
            self::REJECTED => 'Rejected',
            self::WITHDRAWN => 'Withdrawn',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::APPLIED => 'info',
            self::SCREENING => 'primary',
            self::INTERVIEW => 'warning',
            self::OFFERED => 'success',
            self::HIRED => 'success',
            self::REJECTED => 'danger',
            self::WITHDRAWN => 'secondary',
        };
    }

    public function order(): int
    {
        return match($this) {
            self::APPLIED => 1,
            self::SCREENING => 2,
            self::INTERVIEW => 3,
            self::OFFERED => 4,
            self::HIRED => 5,
            self::REJECTED => 99,
            self::WITHDRAWN => 99,
        };
    }
}
