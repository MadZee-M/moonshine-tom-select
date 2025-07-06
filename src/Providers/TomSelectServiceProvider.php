<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Providers;

use Illuminate\Support\ServiceProvider;

final class TomSelectServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'moonshine-tom-select');

        $this->publishes([
            __DIR__ . '/../../public' => public_path('vendor/moonshine-tom-select'),
        ], ['moonshine-tom-select-assets', 'laravel-assets']);
    }
}
