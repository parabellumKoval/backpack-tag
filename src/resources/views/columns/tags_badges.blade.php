{{-- Custom column for displaying tags as badges with background color from tag record --}}
@php
    $value = data_get($entry, $column['name']);
    
    // Handle both collections and arrays
    if ($value instanceof \Illuminate\Support\Collection) {
        $tags = $value;
    } elseif (is_array($value)) {
        $tags = collect($value);
    } else {
        $tags = collect();
    }
@endphp

<span>
    @if($tags->count())
        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
            @foreach($tags as $tag)
                <span style="
                    display: inline-flex;
                    align-items: center;
                    background-color: {{ $tag->color ?? '#6c757d' }};
                    border: 2px solid {{ $tag->color ?? '#6c757d' }};
                    color: #ffffff;
                    padding: 4px 10px;
                    height: 30px;
                    border-radius: 15px;
                    font-size: 12px;
                    font-weight: 700;
                    white-space: nowrap;
                ">
                    @if(!empty($tag->icon))
                        <img src="{{ $tag->icon }}" 
                             alt="{{ $tag->label ?? $tag->value }}"
                             style="width: 26px; height: 26px; object-fit: cover; margin: 2px 6px 2px -10px; background: rgba(255, 255, 255, 0.3); border-radius: 12px;">
                    @endif
                    {{ $tag->label ?? $tag->value ?? 'N/A' }}
                </span>
            @endforeach
        </div>
    @else
        <span class="text-muted">—</span>
    @endif
</span>
