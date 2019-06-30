<?php
/**
 * Project      : work-studio
 * File Name    : TestController.php
 * User         : Abu Bakar Siddique
 * Email        : absiddique.live@gmail.com
 * Date[Y/M/D]  : 2019/06/26 6:28 PM
 */

namespace TunnelConflux\DevCrud\Http\Controllers;

class TestController extends DevCrudController
{
    public function index()
    {
        return view('easy-crud::contact');
    }
}