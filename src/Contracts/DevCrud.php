<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudTrait.php
 * Author       : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/29 6:36 PM
 */

namespace TunnelConflux\DevCrud\Contracts;

use TunnelConflux\DevCrud\Requests\SaveFormRequest;
use TunnelConflux\DevCrud\Requests\UpdateFormRequest;

interface DevCrud
{
    function index();

    function create();

    function store(SaveFormRequest $request);

    function show();

    function edit();

    function update(UpdateFormRequest $request);

    function destroy();

    function getValidationRules(): array;

    function getValidationMessages(): array;
}
