<?php

namespace TunnelConflux\DevCrud\Requests;

use Illuminate\Foundation\Http\FormRequest;
use TunnelConflux\DevCrud\Helpers\DevCrudHelper;
use TunnelConflux\DevCrud\Models\DevCrudModel;
use TunnelConflux\DevCrud\Models\Enums\InputTypes;
use TunnelConflux\DevCrud\Traits\DevCrudTrait;

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
         * @var \TunnelConflux\DevCrud\Controllers\DevCrudController
         */
        $controller = $this->route()->controller ?? null;
        $model = $controller->getModel() ?? null;
        $fields = [];

        if (!$model instanceof DevCrudModel && !$controller instanceof DevCrudTrait) {
            return $fields;
        }

        $rules = $controller->getValidationRules() ?: [];
        $mFields = (count($controller->formRequiredItems) > 0) ? $controller->formRequiredItems : $model->getFillable();

        foreach ($mFields as $field) {
            $fields[$field] = !in_array($field, $controller->formIgnoreItems) ? ['required'] : ['nullable'];

            $this->checkFile($field, $fields, $model);

            if ($rules[$field] ?? null) {
                $fields[$field] = $rules[$field];
            }
        }

        return $fields;
    }

    public function messages()
    {
        return $this->route()->controller->getValidationMessages();
    }

    protected function checkFile($field, &$fields, $model)
    {
        if (in_array($field, $model->getInputTypes()[InputTypes::IMAGE])) {
            DevCrudHelper::arrayPush($fields[$field], 'image');
        } elseif (in_array($field, $model->getInputTypes()[InputTypes::FILE])) {
            DevCrudHelper::arrayPush($fields[$field], 'file');
        }
    }
}
