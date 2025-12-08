<?php

namespace App\Modules\Main\Enums;

class OtpStatusEnum extends BaseEnum
{
    const Pending = 0;
    const Sent    = 1;  
    const Failed  = 2;
    const Read    = 3;
}
