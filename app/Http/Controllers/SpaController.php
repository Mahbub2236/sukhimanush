<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class SpaController extends Controller
{
    /**
     * Render the single page application shell.
     */
    public function index(): View
    {
        return view('app');
    }
}
