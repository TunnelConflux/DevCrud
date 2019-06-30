<?php

namespace TunnelConflux\DevCrud\Http\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use TunnelConflux\DevCrud\Http\Interfaces\DevCrudRequestInterface;

abstract class DevCrudFormDevCrudRequest extends FormRequest implements DevCrudRequestInterface
{
}