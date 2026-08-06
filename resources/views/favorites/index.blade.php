@extends('layouts.app', ['title' => __('ui.favorites')])

@section('content')
<div id="favorites-app" class="site-shell py-10 sm:py-14" data-favorites-page data-delete-url="{{ url('/favorites') }}" data-favorites-url="{{ route('favorites.store') }}" data-confirm="{{ __('ui.confirm_remove') }}" data-csrf="{{ csrf_token() }}">
    <section class="animate-rise">
        <p class="section-kicker">{{ __('ui.eyebrow') }}</p>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-5">
            <h1 class="display-title">{{ __('ui.favorites') }}</h1>
            <a href="{{ route('movies.index') }}" class="button-secondary">{{ __('ui.browse_movies') }} <span aria-hidden="true">↗</span></a>
        </div>
    </section>
    @if ($favorites->isEmpty())
        <div class="empty-state mt-14 animate-rise"><span class="empty-index">00</span><h2>{{ __('ui.empty_favorites_title') }}</h2><p>{{ __('ui.empty_favorites_copy') }}</p></div>
    @else
        <div class="movie-grid mt-14">
            @foreach ($favorites as $favorite)
                @include('movies._card', ['movie' => ['imdb_id' => $favorite->imdb_id, 'title' => $favorite->title, 'year' => $favorite->year, 'type' => $favorite->type ?: 'movie', 'poster' => $favorite->poster], 'favoriteIds' => [$favorite->imdb_id]])
            @endforeach
        </div>
    @endif
</div>
@endsection
