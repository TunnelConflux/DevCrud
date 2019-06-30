<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudRequestInterface.php
 * User         : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/30 12:28 PM
 */

namespace TunnelConflux\DevCrud\Http\Interfaces;

interface DevCrudRequestInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize();

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules();

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages();

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes();
}