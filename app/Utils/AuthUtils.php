<?php

namespace App\Utils;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthUtils
{
    public static function checkAuthenticationAndRoles(array $roles)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return false;
        }
        if (!$user || !$user->hasAnyRole($roles)) {
            return false;
        }

        return true;
    }
}