@props([
    'title',
    'description' => null,
])

<div class="page-header">
    <div class="min-w-0">
        <h1 class="page-title">{{ $title }}</h1>
        @if($description)
            <p class="page-description">{{ $description }}</p>
        @endif
    </div>

    @if(isset($actions) && $actions->isNotEmpty())
        <div class="page-actions">
            {{ $actions }}
        </div>
    @endif
</div>
