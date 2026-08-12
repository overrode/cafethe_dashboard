<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Class AboutController
 * @package App\Controllers
 */
class AboutController extends Controller
{
    /** 
     * Display the about page.
     *
     * @return void
     */
    public function index(): void
    {
        $this->view('frontend/about/index', [
            'title' => 'À propos - CafThé',
        ]);
    }

}