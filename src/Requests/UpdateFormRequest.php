<?php

namespace TunnelConflux\DevCrud\Requests;

use Exception;
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
        $fields = parent::rules();
        /**
         * @var \TunnelConflux\DevCrud\Controllers\DevCrudController
         */
        $controller = $this->route()->controller ?? null;

        if (Route::is('*.edit') && count($controller->formIgnoreItemsOnUpdate) > 0) {
            foreach ($fields as $key => $val) {
                if (in_array($key, $controller->formIgnoreItemsOnUpdate)) {
                    try {
                        if (is_string($fields[$key])) {
                            $fields[$key] = str_replace("required", "nullable", $fields[$key]);
                        } elseif (is_array($fields[$key]) && count((array)$fields[$key]) > 0) {
                            $fields[$key] = array_map(function ($v) {
                                return ($v == "required") ? "nullable" : $v;
                            }, $fields[$key]);
                        } else {
                            $fields[$key] = ["nullable"];
                        }
                    } catch (Exception $e) {

                    }
                }
            }
        }

        return $fields;
    }
}