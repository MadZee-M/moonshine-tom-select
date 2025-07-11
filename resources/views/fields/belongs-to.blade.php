@props([
    'value' => '',
    'values' => [],
    'isNullable' => false,
    'isSearchable' => false,
    'isAsyncSearch' => false,
    'asyncSearchUrl' => '',
    'isCreatable' => false,
    'createButton' => '',
    'fragmentUrl' => '',
    'relationName' => '',
    'isNative' => false,

    'settings' => [],
    'plugins' => [],
])
@if($isCreatable)
{!! $createButton !!}

<x-moonshine::layout.divider />

@fragment($relationName)
<div
    x-data="fragment('{{ $fragmentUrl }}')"
    @defineEvent('fragment_updated', $relationName, 'fragmentUpdate')
>
@endif

<x-moonshine-tom-select::fields.select
        :attributes="$attributes"
        :nullable="$isNullable"
        :searchable="$isSearchable"
        :value="$value"
        :values="$values"
        :asyncRoute="$isAsyncSearch ? $asyncSearchUrl : null"
        :native="$isNative"
        :settings="$settings"
        :plugins="$plugins"
/>

@if($isCreatable)
</div>
@endfragment
@endif
