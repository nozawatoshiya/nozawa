<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChengePassRequest extends FormRequest
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
          'pass'=>'required|min:4|max:15|alpha_num',
          'newpass'=>'required|min:4|max:15|alpha_num',
          'checkpass'=>'required|min:4|max:15|alpha_num',
        ];
    }
}
