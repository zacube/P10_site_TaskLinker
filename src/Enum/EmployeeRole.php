<?php

namespace App\Enum;

enum EmployeeRole: string
{
    case User = 'ROLE_USER';
    case Manager = 'ROLE_MANAGER';
}
