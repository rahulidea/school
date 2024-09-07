<?php
namespace App\Http\Requests\Holiday;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date|unique:holidays,date,' . $this->holiday,
            'name' => 'nullable|string|max:255',
        ];
    }
}