<?php
/**
 * Project      : DevCrud
 * File Name    : DevCrudInterface.php
 * User         : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/30 12:28 PM
 */

namespace TunnelConflux\DevCrud\Http\Interfaces;

interface DevCrudInterface
{
    public function index();

    public function create();

    public function store(DevCrudRequestInterface $request, DevCrudModelInterface $model);

    public function show();

    public function edit();

    public function update(DevCrudRequestInterface $request, DevCrudModelInterface $model);

    public function destroy();
}