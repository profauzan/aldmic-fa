@php
    $isFavorite = in_array($movie['imdb_id'], $favoriteIds ?? [], true);
    $poster = $movie['poster'] ?? null;
@endphp
<article class="movie-card reveal" data-movie-card data-imdb-id="{{ $movie['imdb_id'] }}" data-title="{{ $movie['title'] }}" data-year="{{ $movie['year'] }}" data-type="{{ $movie['type'] }}" data-poster="{{ $poster }}">
    <a href="{{ route('movies.show', $movie['imdb_id']) }}" class="poster-frame group">
        @if ($poster)
            <img src="{{ $poster }}" data-src="{{ $poster }}" alt="{{ $movie['title'] }} poster" loading="lazy" class="poster-image">
        @else
            <div class="poster-empty"><span>{{ __('ui.poster_unavailable') }}</span></div>
        @endif
        <span class="poster-overlay">{{ __('ui.view_details') }} <span aria-hidden="true">↗</span></span>
    </a>
    <div class="pt-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate font-display text-base font-bold text-paper">{{ $movie['title'] }}</p>
                <p class="mt-1 font-mono text-[10px] uppercase tracking-[0.16em] text-muted">{{ $movie['year'] }} <span class="px-1 text-amber">/</span> {{ __('ui.' . $movie['type']) }}</p>
            </div>
            <button type="button" data-favorite data-favorite-id="{{ $movie['imdb_id'] }}" data-favorite-state="{{ $isFavorite ? 'true' : 'false' }}" class="favorite-button {{ $isFavorite ? 'is-favorite' : '' }}" aria-label="{{ $isFavorite ? __('ui.remove_favorite') : __('ui.add_favorite') }}" aria-pressed="{{ $isFavorite ? 'true' : 'false' }}" title="{{ $isFavorite ? __('ui.remove_favorite') : __('ui.add_favorite') }}">
                <svg viewBox="0 0 24 24" fill="{{ $isFavorite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.7" class="h-4 w-4"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>
</article>
