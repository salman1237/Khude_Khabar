<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGrowthRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'recorded_date' => ['required', 'date', 'before_or_equal:today'],
            'weight_kg' => ['required', 'numeric', 'min:0', 'max:150'],
            'height_cm' => ['required', 'numeric', 'min:0', 'max:250'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'recorded_date' => 'তারিখ',
            'weight_kg' => 'ওজন',
            'height_cm' => 'উচ্চতা',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'recorded_date.required' => 'তারিখ নির্বাচন করুন।',
            'recorded_date.before_or_equal' => 'তারিখ আজকের পরে হতে পারবে না।',
            'weight_kg.required' => 'ওজন লিখুন।',
            'weight_kg.numeric' => 'ওজন অবশ্যই সংখ্যা হতে হবে।',
            'height_cm.required' => 'উচ্চতা লিখুন।',
            'height_cm.numeric' => 'উচ্চতা অবশ্যই সংখ্যা হতে হবে।',
        ];
    }
}
