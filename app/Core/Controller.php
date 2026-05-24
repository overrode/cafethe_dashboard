<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Handles rendering of a specified view with optional data.
 *
 * @param string $view The name of the view file to be rendered.
 * @param array $data Optional associative array of data to extract and pass to the view.
 *
 * @return void
 */
class Controller
{
    /**
     * Renders the specified view file and passes data to it.
     *
     * @param string $view The name of the view file to render, without the file extension.
     * @param array $data An associative array of data that will be extracted and made available to the view.
     * @return void
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data);

        require __DIR__ . '/../Views/' . $view . '.php';
    }
}
