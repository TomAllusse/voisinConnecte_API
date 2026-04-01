<?php

namespace App\Enum;

enum Report_Reason: string
{
    case Spam = 'spam';
    case Inappropriate = 'inappropriate';
    case Scam = 'scam';
    case Other = 'other';
}
