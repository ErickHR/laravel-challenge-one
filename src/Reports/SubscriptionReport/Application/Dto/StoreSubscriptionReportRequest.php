<?php

namespace Src\Reports\SubscriptionReport\Application\Dto;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => 'date',
            'to' => 'date|after_or_equal:from',
        ];
    }
}
