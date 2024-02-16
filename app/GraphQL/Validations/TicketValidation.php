<?php

namespace App\GraphQL\Validations;

use Illuminate\Support\Facades\Validator;

class TicketValidation
{
    public static function make(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'occupation_area' => ['required', 'string', 'max:120'],
        ];

        if (!is_null($id)) {
            $adaptativeRules = [];
            foreach ($rules as $property => $propertyRules) {
                foreach ($propertyRules as $rule) {
                    if ($rule !== 'required') {
                        $adaptativeRules[$property][] = $rule;
                    }
                }
            }
            $rules = $adaptativeRules;
        }

        $validator = Validator::make($data, $rules);

        return $validator;
    }
}