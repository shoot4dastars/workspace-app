<?php

namespace App\Enums;

enum Role: string
{
    case owner = 'owner';
    case admin = 'admin';
    case member = 'member';
}
