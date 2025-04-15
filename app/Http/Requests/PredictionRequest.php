<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PredictionRequest extends FormRequest
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
            'student_id'          => 'nullable|exists:students,id',
            'subject_id'          => 'required|exists:subjects,id',
            'attendance'          => 'required|integer|between:0,100',
            'hours_studied'       => 'required|numeric|between:0,168',
            'previous_scores'     => 'required|numeric|between:0,100',
            'sleep_hours'         => 'required|numeric|between:0,24',
            'tutoring_sessions'   => 'required|integer|min:0',
            'peer_influence'      => 'required|in:positive,neutral,negative',
            'motivation_level'    => 'required|in:low,medium,high',
            'teacher_quality'     => 'required|in:low,medium,high',
            'access_to_resources' => 'required|in:low,medium,high',
            // 'save_result'         => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'hours_studied'   => str_replace(',', '.', $this->hours_studied),
            'previous_scores' => str_replace(',', '.', $this->previous_scores),
            'sleep_hours'     => str_replace(',', '.', $this->sleep_hours),

            'tutoring_sessions' => $this->tutoring_sessions ?? 0,
        ]);
    }
<<<<<<< HEAD

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'The given data was invalid',
            'errors' => $validator->errors(),
        ], 422));
    }
=======
>>>>>>> parent of f1798b2 (Merge branch 'main' of https://github.com/RidyCh/api.nilaiku)
}
