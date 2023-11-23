<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LocationsRequest
 * @package App\Http\Requests
 *
 * @property float latitude
 * @property float longitude
 */
class LocationsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        return [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }
}
