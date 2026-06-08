<?php

if (! function_exists('directorPage')) {
    /**
     * Render a department view inside the director (read-only) shell.
     */
    function directorPage(string $view, array $data = [])
    {
        return view($view, array_merge($data, [
            '__layout' => 'director.layout',
            'readOnly' => true,
        ]));
    }
}
