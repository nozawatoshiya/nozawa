<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistRequest extends FormRequest
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
          'date'=>'required|date',
          'category'=>'required',
          'stime'=>'between:1,12|',
          'ftime'=>'between:1,12|',
          'btime'=>'between:1,12|',
        ];
    }
}
