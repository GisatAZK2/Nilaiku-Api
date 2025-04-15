<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'name'      => 'required|string|max:255',
            'age'       => 'required|integer|min:0',
            'gender'    => 'required|string|in:male,female',
            'education' => 'required|string|max:255',
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

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'The given data was invalid',
            'errors' => $validator->errors(), 
        ], 422)); 
    }
}
