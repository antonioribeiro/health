<div class="col-md-{{ isset($columnSize) ? $columnSize : 12 }}">
    <div class="btn btn-block btn-{{ $itemHealth ? 'success' : 'danger' }} {{ config('health.style.button_lines') }}" title="{{ $itemMessage }}">
        @if (config('health.style.button_lines', 'multi') == 'multi')
            <h2>
                {{ $itemName }}
            </h2>

            <h3>
                <i class="fa fa-{{ $itemHealth ? 'check-circle' : 'times-circle' }}"></i>
            </h3>
        @else
            <h3>
                <i class="fa fa-{{ $itemHealth ? 'check-circle' : 'times-circle' }}"></i>
                {{ $itemName }}
            </h3>
        @endif
    </div>
</div>
