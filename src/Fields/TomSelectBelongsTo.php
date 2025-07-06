<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Fields;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;

class TomSelectBelongsTo extends BelongsTo {
    protected string $view = 'moonshine-tom-select::fields.belongs-to';
}
