<?php

namespace App\Enum;

enum EmployeeStatus: string
{
    case CDI = 'CDI';
    case CDD = 'CDD';
    case Interim = 'Intérim';

}
