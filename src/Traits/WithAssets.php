<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Traits;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;

trait WithAssets {
    protected function assets(): array {
        return [
            Js::make('vendor/moonshine-tom-select/init.min.js'),
            Css::make('vendor/moonshine-tom-select/tom-select.css'),
        ];
    }
}