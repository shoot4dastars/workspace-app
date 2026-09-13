<?php

namespace App\Enums;

enum TaskStatus: string
{
    case todo = 'todo';
    case in_progress = 'in_progress';
    case done = 'done';
}
