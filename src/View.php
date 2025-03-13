<?php

namespace NovaPhp\Core;

use Jenssegers\Blade\Blade;
use Valitron\Validator;

class View
{

    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function show($view, $data = [])
    {
        $blade = new Blade(config('app.paths.view'), config('app.paths.views_cache'));
        $blade->share('errors', $this->validator->errors());
        if (config('app.debug')) {
            $bladeCache = glob(config('app.paths.views_cache') . '*');
            foreach ($bladeCache as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        return $blade->render($view, $data);
    }
}