<?php

namespace App\Http\Requests;

class PhoneVerifyRequest extends BaseRequest
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
            'phone' => 'required|exists:users',
        ];
    }

    /**
     * the data of above request
     *
     * @return array
     */
    public function data()
    {
        return [
            'phone' => request()->phone,
            'code' => mt_rand(100000, 999999),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
