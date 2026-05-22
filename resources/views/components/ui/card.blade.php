@props(['bodyClass' => ''])

<div {{ $attributes->merge(['class' => 'ui-card']) }}>
    <div class="ui-card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>
</div>
