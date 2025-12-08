<?php

namespace App\Modules\Main\Enums;

class PaymentStatusEnum extends BaseEnum
{
    const Pending    = "Pending";  
    const Processing = "Processing";  
    const Paid       = "Paid";  
    const Cancelled  = "Cancelled";  
    const Rejected   = "Rejected";  
    const Failed     = "Failed";   
}
