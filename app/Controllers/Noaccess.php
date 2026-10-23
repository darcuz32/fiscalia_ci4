<?php
namespace App\Controllers;

class Noaccess extends BaseController
{
    public function index()
    {
        return view('noaccess_view');
    }
}