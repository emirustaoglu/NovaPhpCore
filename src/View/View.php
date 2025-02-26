<?php

namespace NovaCore\View;

use Jenssegers\Blade\Blade;

class View
{
    private static ?Blade $blade = null;

    /**
     * Blade instance'ını başlatır
     */
    public static function init(): void
    {
        if (self::$blade === null) {
            $viewPath = config('view.paths', [resource_path('views')]);
            $cachePath = config('view.cache', storage_path('app/cache'));

            self::$blade = new Blade($viewPath, $cachePath);
        }
    }

    /**
     * View render eder
     *
     * @param string $view View dosyası
     * @param array $data View'e gönderilecek veriler
     * @return string
     */
    public static function render(string $view, array $data = []): string
    {
        self::init();
        return self::$blade->make($view, $data)->render();
    }

    /**
     * Blade instance'ına erişim sağlar
     * Özel direktifler eklemek vb. için kullanılabilir
     */
    public static function blade(): Blade
    {
        self::init();
        return self::$blade;
    }
}
