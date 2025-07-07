# [Tom select](https://tom-select.js.org) field for [MoonShine Laravel admin panel](https://moonshine-laravel.com)

---

## Compatibility

|      MoonShine       | Moonshine Tom select | Currently supported |
|:--------------------:|:--------------------:|:-------------------:|
|       >= v3.0        |      >= v1.0.0       |         yes         |

## Installation
```shell
composer require MadZee-M/moonshine-tom-select
```

## Usage

```php
use MadZeeM\MoonshineTomSelect\Fields\TomSelect;


TomSelect::make('Select')
```
> Данное поле полностью совместима с текущим основным полем MoonShine, поэтому можно сделать так, для быстрой интеграции:
> ```diff
> - use MoonShine\UI\Fields\Select;
> + use MadZeeM\MoonshineTomSelect\Fields\TomSelect as Select;
> ```

Также вместо `BelongsTo` и `BelongsToMany` можно воспользоваться
```php
use MadZeeM\MoonshineTomSelect\Fields\TomSelectBelongsTo;
use MadZeeM\MoonshineTomSelect\Fields\TomSelectBelongsToMany;

TomSelectBelongs::make('Select')
TomSelectBelongsToMany::make('Select')
```

> И точно также для быстрой интеграции, можно сделать:
> ```diff
> - use MoonShine\Laravel\Fields\Relationships\BelongsTo;
> + use MadZeeM\MoonshineTomSelect\Fields\TomSelectBelongsTo as BelongsTo;
> 
> - use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
> + use MadZeeM\MoonshineTomSelect\Fields\TomSelectBelongsToMany as BelongsToMany;
> ```

## Plugins

The `addPlugins()` method allows you to add new plugins to the default plugins

> Official plugins: https://tom-select.js.org/plugins

```php
addPlugins(array|string $plugin, array $pluginOptions = [])
```
```php
TomSelect::make('Select')
    ->addPlugins('checkbox_options', [...])
    
TomSelect::make('Select')
    ->addPlugins(['checkbox_options', '...'])
    
TomSelect::make('Select')
    ->addPlugins([
        'checkbox_options' => [...]
    ])
```

Вы можете создавать собственные плагины.
```html
<script>
    document.addEventListener('moonshine:select.add_plugin', function({ detail: createPlugin }) {
        createPlugin('myPlugin', function(pluginOptions) {
            console.log(pluginOptions, this.getValue())

            this.on('change', value => {
                //
            })
        })
    })
</script>
```
```php
TomSelect::make('Select')
    ->addPlugins('myPlugin', [
        'foo' => 'bar'
    ])
```
> Full documentation: https://tom-select.js.org/docs/plugins


## Settings

Этот `settings()` метод разрешает использовать все пользовательские настройки
> Все доступные настройки: https://tom-select.js.org/docs/#general-configuration

```php
settings(array $settings)
```
```php
TomSelect::make('Select')
    ->settings([
        'maxOptions' => 100,
        'maxItems' => 100,
        'hideSelected' => false
    ])
```

---

Для всех именных настроек, есть очень удобный метод `fieldNames()`
```php
fieldNames(
    ?string $valueField = null,         // default: value
    ?string $labelField = null,         // default: label
    ?string $descriptionField = null,   // default: description

    ?string $childrenField = null,      // default: values
    ?string $optgroupValueField = null, // default: value
    ?string $optgroupLabelField = null, // default: label
    ?string $optgroupField = null,      // default: optgroup

    ?array $searchField = null,         // default: ['label']
    ?string $disabledField = null,      // default: disabled
    ?string $sortField = null           // default: $order
)
```
```php
TomSelect::make('Select')
    ->fieldNames(
        valueField: 'id',
        labelField: 'name',
        childrenField: 'children',
    )
```

---

Для дополнительной настройки асинхронности, можно воспользоваться методом `asyncSettings()`
```php
asyncSettings(array $settings)
```
```php
TomSelect::make('Select')
    ->asyncSettings([
        // Можно менять название поля поиска
        'queryKey' => 'query',
        
        // Можно отправить текущие активные значения, просто указываем название 
        'selectedValuesKey' => '_value',
        
        // Если результат обвернуть например в data, то указываем этот ключ
        'resultKey' => 'data',
        
        // Если хотите, чтобы вместе с запросом, шли все поля текущей формы, то задаем TRUE
        'withAllFields' => false,
    ])
```
---


Этот `asyncOnInit()` метод теперь поддерживает "Загрузчик", после открытия селекта

```php
asyncOnInit(bool $whenOpen = true, bool $withLoading = false)
```
```php
TomSelect::make('Select')
    ->asyncOnInit(withLoading: true)
```

---

> С этим `multiple()` методом автоматически задается поиск, если хотите отключить, просто указываем `withoutSearchable()` после метода
>```php
> TomSelect::make('Select')
>   ->multiple()
>   ->withoutSearchable()
>```

