<?php

namespace MadZeeM\MoonshineTomSelect;

use Illuminate\Support\Facades\Vite;

class Helper
{
    public static function isRunningViteDevServer(): bool {
        if (static::isEnableViteDevServer()) {
            Vite::useHotFile($hotPath = base_path(env('MOONSHINE_TOM_SELECT_HOT')));
            return file_exists($hotPath);
        }

        return false;
    }

    public static function isEnableViteDevServer(): bool {
        return !! env('MOONSHINE_TOM_SELECT_HOT');
    }
}