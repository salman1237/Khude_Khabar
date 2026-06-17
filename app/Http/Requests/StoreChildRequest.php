<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'age_months' => ['required', 'integer', 'min:0', 'max:216'],
            'gender' => ['required', 'in:boy,girl'],
            'weight_kg' => ['nullable', 'numeric', 'min:0', 'max:150'],
            'height_cm' => ['nullable', 'numeric', 'min:0', 'max:250'],
            'budget_preference' => ['required', 'in:low,medium,high'],
            'allergies' => ['nullable', 'array'],
            'allergies.*' => ['nullable', 'string', 'max:255'],
            'health_conditions' => ['nullable', 'array'],
            'health_conditions.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'শিশুর নাম',
            'age_months' => 'বয়স',
            'gender' => 'লিঙ্গ',
            'weight_kg' => 'ওজন',
            'height_cm' => 'উচ্চতা',
            'budget_preference' => 'বাজেট',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'শিশুর নাম লিখুন।',
            'age_months.required' => 'শিশুর বয়স (মাসে) লিখুন।',
            'age_months.integer' => 'বয়স অবশ্যই সংখ্যা হতে হবে।',
            'age_months.max' => 'বয়স সর্বোচ্চ ২১৬ মাস (১৮ বছর) হতে পারে।',
            'gender.required' => 'শিশুর লিঙ্গ নির্বাচন করুন।',
            'gender.in' => 'লিঙ্গ অবশ্যই ছেলে বা মেয়ে হতে হবে।',
            'weight_kg.numeric' => 'ওজন অবশ্যই সংখ্যা হতে হবে।',
            'height_cm.numeric' => 'উচ্চতা অবশ্যই সংখ্যা হতে হবে।',
            'budget_preference.required' => 'বাজেট নির্বাচন করুন।',
        ];
    }
}
