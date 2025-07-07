<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Fields;

use Closure;
use MadZeeM\MoonshineTomSelect\Traits\WithAssets;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

class TomSelectBelongsTo extends BelongsTo {
    use WithAssets;

    protected string $view = 'moonshine-tom-select::fields.belongs-to';

    protected function prepareBeforeRender(): void {
        parent::prepareBeforeRender();

        $this->customAttributes(array_filter([
            'data-async-selected-values-key' => 'value',
            'data-async-with-all-fields' => true,
        ]));
    }
}
