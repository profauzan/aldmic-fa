@extends('layouts.app', ['title' => __('ui.movies')])

@section('content')
<div id="movie-app" class="site-shell py-10 sm:py-14" data-movie-app data-search-url="{{ route('movies.search') }}" data-detail-url="{{ url('/movies') }}" data-favorites-url="{{ route('favorites.store') }}" data-delete-url="{{ url('/favorites') }}" data-csrf="{{ csrf_token() }}" data-page="1" data-total="{{ $total }}" data-query="{{ $initialQuery }}" data-type="{{ $initialType }}" data-year="{{ $filters['year'] }}">
    <section class="search-hero animate-rise">
        <div class="max-w-2xl">
            <p class="section-kicker">{{ __('ui.search_kicker') }}</p>
            <h1 class="display-title mt-3">{{ __('ui.search_title') }}</h1>
            <p class="mt-5 max-w-xl text-sm leading-7 text-muted">{{ __('ui.search_copy') }}</p>
        </div>
        <form id="movie-search" class="search-form mt-9" data-search-form>
            <div class="search-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-5 w-5 text-muted"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4" stroke-linecap="round"/></svg>
                <label class="sr-only" for="query">{{ __('ui.search') }}</label>
                <input id="query" name="q" value="{{ $filters['q'] }}" placeholder="{{ __('ui.search_placeholder') }}" class="search-input" autocomplete="off" required>
            </div>
            <label class="sr-only" for="type">{{ __('ui.type') }}</label>
            <select id="type" name="type" class="select-control">
                <option value="">{{ __('ui.all_types') }}</option>
                <option value="movie" {{ ($filters['type'] ?: $initialType) === 'movie' ? 'selected' : '' }}>{{ __('ui.movie') }}</option>
                <option value="series" {{ $filters['type'] === 'series' ? 'selected' : '' }}>{{ __('ui.series') }}</option>
                <option value="episode" {{ $filters['type'] === 'episode' ? 'selected' : '' }}>{{ __('ui.episode') }}</option>
            </select>
            <label class="sr-only" for="year">{{ __('ui.year') }}</label>
            <input id="year" name="year" value="{{ $filters['year'] }}" placeholder="{{ __('ui.year') }}" inputmode="numeric" maxlength="4" class="text-control year-control">
            <button class="button-primary search-button" type="submit">{{ __('ui.search') }} <span aria-hidden="true">↗</span></button>
        </form>
    </section>

    <section class="mt-14" aria-live="polite">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="section-kicker">{{ __('ui.movies') }}</p>
                <p data-result-label class="mt-2 font-mono text-xs uppercase tracking-[0.16em] text-muted">{{ $total ? $total . ' ' . __('ui.results') : '' }}</p>
            </div>
            <div data-loading-label class="hidden items-center gap-2 font-mono text-[10px] uppercase tracking-[0.16em] text-amber"><span class="loading-dot"></span>{{ __('ui.loading') }}</div>
        </div>

        @if ($error)
            <div data-error class="notice notice-error">{{ $error }}</div>
        @endif
        <div data-empty class="empty-state {{ (! $error && count($movies) === 0) ? '' : 'hidden' }}">
            <span class="empty-index">00</span><h2>{{ __('ui.no_results_title') }}</h2><p>{{ __('ui.no_results_copy') }}</p>
        </div>
        <div data-start class="empty-state hidden">
            <span class="empty-index">01</span><h2>{{ __('ui.start_title') }}</h2><p>{{ __('ui.start_copy') }}</p>
        </div>
        <div data-movie-grid class="movie-grid {{ count($movies) ? '' : 'hidden' }}">
            @foreach ($movies as $movie)
                @include('movies._card', ['movie' => $movie, 'favoriteIds' => $favoriteIds])
            @endforeach
        </div>
        <div data-sentinel class="h-12"></div>
        <div data-load-more class="hidden justify-center py-6"><span class="loading-ring"></span><span class="sr-only">{{ __('ui.load_more') }}</span></div>
    </section>
</div>
@endsection
