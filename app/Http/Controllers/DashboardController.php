<?php

namespace App\Http\Controllers;

use App\Traits\DateTime;

/**
 * Class DashboardController
 *
 * @package App\Http\Controllers
 * @category Controller
 */
class DashboardController extends Controller
{
    use DateTime;

    public function index()
    {
        return view('dashboard');
    }
}
