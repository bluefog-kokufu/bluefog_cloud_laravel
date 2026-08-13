<?php

namespace App\Http\Requests\Internal;

use Illuminate\Foundation\Http\FormRequest;

class BootstrapRequest extends FormRequest
{
    /**
     * トークン検証はVerifyBootstrapTokenミドルウェアで行う
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
