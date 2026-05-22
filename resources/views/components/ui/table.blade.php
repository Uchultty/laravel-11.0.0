@props([])

<div class="ui-table-wrap">
    <table {{ $attributes->merge(['class' => 'ui-table']) }}>
        {{ $slot }}
    </table>
</div>
