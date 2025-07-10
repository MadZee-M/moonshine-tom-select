<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use MadZeeM\MoonshineTomSelect\Helper;
use MoonShine\AssetManager\Raw;

final class TomSelectServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'moonshine-tom-select');

        Blade::componentNamespace(__DIR__ . '/../../resources/views/components', 'moonshine-tom-select');

        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/moonshine-tom-select'),
        ], ['moonshine-tom-select-assets', 'laravel-assets']);

        $this->runViteDevServer();
    }

    protected function runViteDevServer(): void {
        if (Helper::isRunningViteDevServer()) {
            moonshineAssets()->prepend([
                Raw::make(
                    Vite::__invoke([
                        'resources/js/init.js',
                        'resources/scss/tom-select.scss',
                    ])->toHtml()
                ),
            ]);
        }
    }
}
