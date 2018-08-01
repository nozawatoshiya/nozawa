<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
     *勤怠を登録する時のバリデーションチェック
     */
    public function rules()
    {
        return [
          'id'=>'required|numeric',
          'year'=>'required|between:2017,2100|numeric',
          'month'=>'required|between:1,12|numeric',
        ];
    }
}
