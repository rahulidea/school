<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class BaseFormRequestApi extends FormRequest {
    protected function failedValidation(Validator $validator)
    {
        if (true || $this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]));
        }
        
        throw (new ValidationException($validator))
                    ->errorBag($this->errorBag)
                    ->redirectTo($this->getRedirectUrl());
    }
}
