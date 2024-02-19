<?php

namespace App\GraphQL\Validations;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserValidation
{
    public static function make(array $data)
    {
        $rules = [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'max:50'],
            'role' => ['required', 'integer', 'in:1,2,3,4'],
            'service_area' => ['nullable'],
        ];
        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return $validator;
        }

        $validator->after(function ($validator) use ($data){

            if ($data['role'] == User::ROLE_SUPPORT && !isset($data['service_area'])) {
                $validator->errors()->add('service_area', 'Field service_area required but not provided.');
            }

        });

        return $validator;
    }
}