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

<select
        {{ $attributes->merge([
            'data-search-enabled' => $isSearchable,
            'data-remove-item-button' => $attributes->get('multiple', false) || $isNullable
        ])->when(!$isNative, fn($a) => $a->merge([
            'x-data' => "tomSelect('$asyncUrl', ". json_encode($settings) .", ". json_encode($plugins) .")",
        ]))->when($isNullable && !$isNative && $attributes->get('placeholder') === null, fn($a) => $a->merge(['placeholder' => '-'])) }}
>
    @if($options ?? false)
        {{ $options }}
    @else
        @if($isNullable && !$attributes->has('multiple'))
            <option value="">{{ $attributes->get('placeholder', '-') }}</option>
        @endif

        @foreach($values as $optionValue)
            @if(isset($optionValue['values']))
                <optgroup label="{{ $optionValue['label'] }}">
                    @foreach($optionValue['values'] as $oValue)
                        <option @selected($oValue['selected'] || $attributes->get('value', '') == $oValue['value'])
                                value="{{ $oValue['value'] }}"
                                data-custom-properties='@json($oValue['properties'])'
                        >
                            {{ $oValue['label'] }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected($optionValue['selected'] || $attributes->get('value', '') == $optionValue['value'])
                        value="{{ $optionValue['value'] }}"
                        data-custom-properties='@json($optionValue['properties'])'
                >
                    {{ $optionValue['label'] }}
                </option>
            @endif
        @endforeach
    @endif
</select>