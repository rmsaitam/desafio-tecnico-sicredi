<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoteScheduleSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'option' => 'required|string|in:Y,N',
            'associate_id' => 'required_without:associate.document|exists:associates,id',
            'associate.document' => 'required_without:associate_id|cpf',
            'associate.name' => 'string|min:3'
        ];
    }
}
