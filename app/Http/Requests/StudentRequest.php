<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
            'name'      => 'required|string',
            'age'       => 'required|integer|min:0',
            'gender'    => 'required|string|in:male,female',
            'education' => 'required|string',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name'      => ucwords(strtolower(trim($this->name))),
            'gender'      => strtolower(trim($this->gender)),
            'education' => trim($this->education),
        ]);
    }
}
