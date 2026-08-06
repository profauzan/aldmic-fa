@extends('layouts.app', ['title' => $movie['title']])

@section('content')
<div class="site-shell py-10 sm:py-14">
    <a href="{{ route('movies.index', request()->query()) }}" class="back-link animate-rise"><span aria-hidden="true">←</span> {{ __('ui.back_to_movies') }}</a>
    <article class="detail-layout mt-10 animate-rise">
        <div class="detail-poster poster-frame">
            @if ($movie['poster'])
                <img src="{{ $movie['poster'] }}" alt="{{ $movie['title'] }} poster" class="poster-image" loading="eager">
            @else
                <div class="poster-empty"><span>{{ __('ui.poster_unavailable') }}</span></div>
            @endif
        </div>
        <div class="detail-copy">
            <div class="flex flex-wrap items-center gap-3 font-mono text-[10px] uppercase tracking-[0.18em] text-amber">
                <span>{{ $movie['year'] }}</span><span class="text-muted">/</span><span>{{ __('ui.' . $movie['type']) }}</span>
                @if ($movie['rating'] && $movie['rating'] !== 'N/A')<span class="text-muted">/</span><span>IMDb {{ $movie['rating'] }}</span>@endif
            </div>
            <h1 class="detail-title mt-5">{{ $movie['title'] }}</h1>
            <div class="mt-7 flex flex-wrap items-center gap-3">
                <button type="button" data-favorite data-favorite-url="{{ route('favorites.store') }}" data-delete-url="{{ url('/favorites') }}" data-csrf="{{ csrf_token() }}" data-favorite-id="{{ $movie['imdb_id'] }}" data-favorite-state="{{ $isFavorite ? 'true' : 'false' }}" data-favorite-title="{{ $movie['title'] }}" data-favorite-year="{{ $movie['year'] }}" data-favorite-type="{{ $movie['type'] }}" data-favorite-poster="{{ $movie['poster'] }}" class="button-secondary favorite-action {{ $isFavorite ? 'is-favorite' : '' }}" aria-pressed="{{ $isFavorite ? 'true' : 'false' }}">
                    <svg viewBox="0 0 24 24" fill="{{ $isFavorite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.7" class="h-4 w-4"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span data-favorite-label>{{ $isFavorite ? __('ui.saved') : __('ui.add_favorite') }}</span>
                </button>
            </div>
            <div class="mt-12 detail-section">
                <p class="section-kicker">{{ __('ui.plot') }}</p>
                <p class="mt-4 max-w-2xl text-base leading-8 text-paper/80">{{ $movie['plot'] !== 'N/A' ? $movie['plot'] : __('ui.unknown') }}</p>
            </div>
            <div class="detail-facts mt-10">
                @foreach ([['released', $movie['released']], ['runtime', $movie['runtime']], ['genre', $movie['genre']], ['director', $movie['director']], ['actors', $movie['actors']], ['awards', $movie['awards']]] as $fact)
                    <div><dt class="fact-label">{{ __('ui.' . $fact[0]) }}</dt><dd class="mt-2 text-sm leading-6 text-paper/80">{{ $fact[1] !== 'N/A' ? $fact[1] : __('ui.unknown') }}</dd></div>
                @endforeach
            </div>
        </div>
    </article>
</div>
@endsection
