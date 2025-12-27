<?php

namespace App\Enums;

enum UserRoleEnum : string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case USER = 'user';
    case RESPONSABLE = 'responsable';
}
