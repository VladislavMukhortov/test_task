<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RefreshRequest
 * @package App\Http\Requests
 *
 * @property string refresh_token
 */
class RefreshRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        return [
            'refresh_token' => 'required|string'
        ];
    }
}
