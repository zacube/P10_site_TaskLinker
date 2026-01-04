<?php

namespace App\Enum;

enum EmployeeRole: string
{
    case Manager = 'ROLE_MANAGER';
    case Collaborateur = 'ROLE_USER';
}
