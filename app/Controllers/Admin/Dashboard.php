<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('admin/dashboard/dashboard');
    }
}
