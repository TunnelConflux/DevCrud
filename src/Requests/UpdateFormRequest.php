<?php

namespace TunnelConflux\DevCrud\Requests;

use Illuminate\Support\Facades\Route;

class UpdateFormRequest extends SaveFormRequest
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
        $rules = parent::rules();
        /**
         * @var \TunnelConflux\DevCrud\Controllers\DevCrudController
         */
        $controller = $this->route()->controller ?? null;

        if (Route::is('*.edit') && count($controller->formIgnoreItemsOnUpdate) > 0) {
            $this->checkNullable($rules, $controller->formIgnoreItemsOnUpdate);
        }

        return $rules;
    }
}
