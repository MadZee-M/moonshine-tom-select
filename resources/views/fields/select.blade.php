@props([
    'value' => '',
    'values' => [],
    'isNullable' => false,
    'isSearchable' => false,
    'asyncUrl' => '',
    'isNative' => false,

    'settings' => [],
    'plugins' => [],
])
<x-moonshine-tom-select::fields.select
        :attributes="$attributes"
        :values="$values"
        :nullable="$isNullable"
        :searchable="$isSearchable"
        :asyncRoute="$asyncUrl"
        :native="$isNative"
        :settings="$settings"
        :plugins="$plugins"
/>
