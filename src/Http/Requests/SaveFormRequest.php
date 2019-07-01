<?php

namespace TunnelConflux\DevCrud\Http\Requests;

use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use TunnelConflux\DevCrud\Models\DevCrudModel;

class SaveFormRequest extends FormRequest
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
        /**
         * @var \TunnelConflux\DevCrud\Http\Controllers\DevCrudController
         */
        $controller = $this->route()->controller ?? null;
        $model      = $controller->model ?? null;

        $fields = [];

        if (!$model instanceof DevCrudModel) {
            return $fields;
        }

        if (count($controller->formRequiredItems) > 0) {
            foreach ($controller->formRequiredItems as $field) {
                $fields[$field] = !in_array($field, $controller->formIgnoreItems) ? ['required'] : ['nullable'];
            }
        } else {
            foreach ($model->getFillable() as $field) {
                $fields[$field] = !in_array($field, $controller->formIgnoreItems) ? ['required'] : ['nullable'];
            }
        }

        if (Route::is('*.edit') && count($controller->formUpdateIgnoreItems) > 0) {
            foreach ($fields as $key => $val) {
                if (in_array($key, $controller->formUpdateIgnoreItems)) {
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