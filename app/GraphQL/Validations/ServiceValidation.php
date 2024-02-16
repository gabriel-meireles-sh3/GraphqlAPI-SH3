<?php

namespace App\GraphQL\Validations;

use Illuminate\Support\Facades\Validator;

class ServiceValidation
{
    public static function make(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;

        $rules = [
            'requester_name' => ['required', 'string', 'max:255', 'exists:tickets,name,deleted_at,NULL'],
            'client_id' => ['required', 'integer', 'exists:tickets,id,deleted_at,NULL'],
            'service_area' => ['required', 'string', 'max:120', 'exists:supports,service_area'],
            'support_id' => ['nullable', 'integer', 'exists:supports,id'],
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
