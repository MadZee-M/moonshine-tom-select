<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Traits;

use MadZeeM\MoonshineTomSelect\Helper;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

trait WithAssets {
    protected function assets(): array {
        if (Helper::isRunningViteDevServer()) {
            return [];
        }

        return [
            Js::make('vendor/moonshine-tom-select/init.min.js'),
            Css::make('vendor/moonshine-tom-select/tom-select.css'),
        ];
    }
}