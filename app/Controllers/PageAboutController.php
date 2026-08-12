<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Class PageAboutController
 * @package App\Controllers
 */
class PageAboutController extends Controller
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