<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Fields;

use MadZeeM\MoonshineTomSelect\Traits\WithAssets;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;

class TomSelectBelongsToMany extends BelongsToMany {
    use WithAssets;

    protected string $view = 'moonshine-tom-select::fields.belongs-to-many';

    protected function prepareBeforeRender(): void {
        parent::prepareBeforeRender();

        $this->customAttributes(array_filter([
            'data-async-selected-values-key' => 'value',
            'data-async-with-all-fields' => true,
        ]));
    }
}
