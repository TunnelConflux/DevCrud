<?php

namespace TunnelConflux\DevCrud\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TunnelConflux\DevCrud\Http\Interfaces\DevCrudRequestInterface;

class SaveRequest extends FormRequest implements DevCrudRequestInterface
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
        return $this->route()->controller->getValidationRules() ?? [];
    }
}