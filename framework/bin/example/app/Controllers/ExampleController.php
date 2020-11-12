<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Class ExampleController
 *
 * @package App\Controllers
 */
class ExampleController extends Controller
{
    /**
     * Returns a generic welcome view.
     *
     * @return \App\Core\Response\View
     */
    public function welcome()
    {
        return view('controllers/example/welcome');
    }
}