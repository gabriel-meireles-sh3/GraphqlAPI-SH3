<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthGraphqlController extends Controller
{
    protected $auth;

    public function graphqlAuthorize(array $allowedRoles = []): bool
    {
        try {
            $this->auth = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $exception) {
            return false;
        }

        if (!$this->auth) {
            return false;
        }

        $userRoles = $this->auth->role;
        foreach ($allowedRoles as $allowedRole) {
            if (in_array($allowedRole, $userRoles)) {
                return true;
            }
        }

        return false;
    }
}
