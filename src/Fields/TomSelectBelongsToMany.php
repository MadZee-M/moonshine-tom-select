<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Fields;

use MoonShine\Laravel\Fields\Relationships\BelongsToMany;

class TomSelectBelongsToMany extends BelongsToMany {
    protected string $view = 'moonshine-tom-select::fields.belongs-to-many';
}
