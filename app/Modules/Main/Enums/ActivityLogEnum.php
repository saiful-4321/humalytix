<?php

namespace App\Modules\Main\Enums;

class ActivityLogEnum extends BaseEnum
{
    const CREATED = 'CREATED';
    const UPDATED = 'UPDATED';
    const DELETED = 'DELETED';
    const BULK_UPLOAD = 'BULK_UPLOAD';
    const DOWNLOAD = 'DOWNLOAD';
}