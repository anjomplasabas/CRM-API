<?php

namespace App\Enum;

enum UserType
{
    case EMAIL;
    case GOOGLE;

    public static function canAuthenticateByPassword(string $type): bool
    {
        return $type == UserType::EMAIL->name;
    }

}
