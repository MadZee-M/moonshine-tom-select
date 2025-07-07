<?php

declare(strict_types=1);

namespace MadZeeM\MoonshineTomSelect\Fields;

use Closure;
use Illuminate\Support\Collection;
use JsonException;
use MadZeeM\MoonshineTomSelect\Traits\WithAssets;
use MoonShine\Contracts\UI\HasAsyncContract;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeNumeric;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Traits\Fields\CanBeMultiple;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\Fields\SelectTrait;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\HasAsync;

class TomSelect extends Field implements
    HasDefaultValueContract,
    CanBeArray,
    CanBeString,
    CanBeNumeric,
    HasUpdateOnPreviewContract,
    HasAsyncContract
{
    use CanBeMultiple {
        CanBeMultiple::multiple as baseMultiple;
    }
    use Searchable;
    use SelectTrait;
    use WithDefaultValue;
    use HasAsync;
    use UpdateOnPreview;
    use HasPlaceholder;
    use WithAssets;

    protected string $view = 'moonshine-tom-select::fields.select';

    protected array $plugins = [];

    protected array $settings = [];

    protected function resolveRawValue(): mixed {
        return $this->resolvePreview();
    }

    /**
     * @throws JsonException
     */
    protected function resolvePreview(): string {
        $value = $this->toValue();

        if ($this->isMultiple()) {
            $value = \is_string($value) && str($value)->isJson() ?
                json_decode($value, true, 512, JSON_THROW_ON_ERROR)
                : $value;

            return collect($value)
                ->when(
                    ! $this->isRawMode(),
                    fn($collect): Collection => $collect->map(
                        fn($v): string => (string) data_get($this->getValues()->flatten(), "$v.label", ''),
                    ),
                )
                ->implode(',');
        }

        if (\is_null($value)) {
            return '';
        }

        return (string) data_get($this->getValues()->flatten(), "$value.label", '');
    }

    public function prepareReactivityValue(mixed $value, mixed &$casted, array &$except): mixed {
        $result = data_get($value, 'value', $value);

        return $this->isMultiple() && \is_array($result)
            ? array_filter($result, static fn($value): bool => $value !== null && $value !== false)
            : $result;
    }

    protected function asyncWith(): void {
        $this->searchable();
        $this->asyncSettings([]);
    }

    public function asyncOnInit(bool $whenOpen = true, bool $withLoading = false): static {
        return $this->customAttributes([
            'data-async-on-init' => true,
            'data-async-on-init-dropdown' => $whenOpen,
            'data-async-on-init-dropdown-with-loading' => $whenOpen && $withLoading,
        ]);
    }

    /**
     * @param array{queryKey: ?string, selectedValuesKey: ?string, resultKey: ?string, withAllFields: bool} $settings
     */
    public function asyncSettings(array $settings): static {
        $settings = array_replace([
            'queryKey' => null, // default: query
            'selectedValuesKey' => null,
            'resultKey' => null,
            'withAllFields' => false,
        ], $settings);

        return $this->customAttributes(array_filter([
            'data-async-query-key' => $settings['queryKey'],
            'data-async-selected-values-key' => $settings['selectedValuesKey'],
            'data-async-result-key' => $settings['resultKey'],
            'data-async-with-all-fields' => $settings['withAllFields'],
        ]));
    }

    public function multiple(bool|Closure|null $condition = null): static {
        return $this
            ->baseMultiple($condition)
            ->searchable();
    }

    public function withoutSearchable(): static {
        $this->searchable = false;
        return $this;
    }

    public function settings(array $settings): static {
        $this->settings = array_merge($this->settings, $settings);
        return $this;
    }

    public function addPlugins(array|string $plugin, array $pluginOptions = []): static {
        if (is_array($plugin)) {
            foreach ($plugin as $name => $options) {
                if (is_numeric($name)) {
                    $name = $options;
                    $options = [];
                }

                $this->addPlugins($name, $options);
            }

            return $this;
        }

        $this->plugins[$plugin] = $pluginOptions;

        return $this;
    }

    public function fieldNames(
        ?string $valueField = null,         // default: value
        ?string $labelField = null,         // default: label
        ?string $descriptionField = null,   // default: description

        ?string $childrenField = null,      // default: values
        ?string $optgroupValueField = null, // default: value
        ?string $optgroupLabelField = null, // default: label
        ?string $optgroupField = null,      // default: optgroup

        ?array  $searchField = null,         // default: ['label']
        ?string $disabledField = null,      // default: disabled
        ?string $sortField = null           // default: $order
    ): static {
        if (is_null($searchField) && ! is_null($labelField)) {
            $searchField = [$labelField];
        }

        return $this->settings(
            array_filter(compact(
                'valueField',
                'labelField',
                'descriptionField',
                'childrenField',
                'optgroupValueField',
                'optgroupLabelField',
                'optgroupField',
                'searchField',
                'disabledField',
                'sortField',
            ))
        );
    }

    protected function viewData(): array {
        return [
            'isSearchable' => $this->isSearchable(),
            'asyncUrl' => $this->getAsyncUrl(),
            'values' => $this->getValues()->toArray(),
            'isNullable' => $this->isNullable(),
            'isNative' => $this->isNative(),
            'settings' => $this->settings,
            'plugins' => $this->plugins,
        ];
    }
}
