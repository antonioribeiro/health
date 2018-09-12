<div class="col-md-{{ isset($columnSize) ? $columnSize : 12 }}" @click="showResult()">
    <div class="btn btn-block btn-{{ $itemHealth ? 'success' : 'danger' }} {{ config('health.style.button_lines') }}"
         title="{{ $itemMessage }}"
         data-name="{{ $itemTitle }}"
         style="opacity: {{ $itemHealth ? '0.5' : '0.85' }};"
    >
        @if (config('health.style.button_lines', 'multi') == 'multi')
            <h2>
                {{ $itemTitle }}
            </h2>
            <p style="font-size: 0.8em; font-weight: 100; margin-top: 1px; color: yellow;">
                @if ($itemSubtitle !== 'default')
                    {{ $itemSubtitle }}
                @else
                    &nbsp;
                @endif
            </p>

            <h3>
                <i class="fa fa-{{ $itemHealth ? 'check-circle' : 'times-circle' }}"></i>
            </h3>
        @else
            <h3>
                <i class="fa fa-{{ $itemHealth ? 'check-circle' : 'times-circle' }}"></i>
                {{ $itemTitle }}
            </h3>
        @endif
    </div>
</div>
