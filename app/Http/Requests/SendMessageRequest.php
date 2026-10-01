<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:5120'],
        ];
    }
}
