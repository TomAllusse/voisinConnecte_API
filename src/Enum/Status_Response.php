<?php

namespace App\Enum;

enum Status_Response: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Refused = 'refused';
}
