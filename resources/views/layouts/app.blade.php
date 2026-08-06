<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="A private, polished movie discovery cabinet.">
    <title>{{ $title ?? __('ui.app_name') }} | {{ __('ui.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-ink text-paper antialiased" data-add-favorite="{{ __('ui.add_favorite') }}" data-remove-favorite="{{ __('ui.remove_favorite') }}" data-favorite-added="{{ __('favorites.added') }}" data-favorite-removed="{{ __('favorites.removed') }}" data-service-error="{{ __('ui.service_error') }}" data-saved="{{ __('ui.saved') }}" data-results="{{ __('ui.results') }}" data-view-details="{{ __('ui.view_details') }}" data-poster-unavailable="{{ __('ui.poster_unavailable') }}">
    <div class="film-grain" aria-hidden="true"></div>

    @auth
        <header class="relative z-20 border-b border-white/10 bg-ink/85 backdrop-blur-xl">
            <div class="site-shell flex h-20 items-center justify-between gap-6">
                <a href="{{ route('movies.index') }}" class="group flex items-center gap-3" aria-label="{{ __('ui.app_name') }}">
                    <span class="brand-mark">FC</span>
                    <span>
                        <span class="block font-display text-lg font-extrabold tracking-tight">{{ __('ui.app_name') }}</span>
                        <span class="hidden font-mono text-[10px] uppercase tracking-[0.22em] text-muted sm:block">{{ __('ui.eyebrow') }}</span>
                    </span>
                </a>
                <nav class="flex items-center gap-2 text-sm font-semibold text-muted" aria-label="Primary navigation">
                    <a href="{{ route('movies.index') }}" class="nav-link {{ request()->routeIs('movies.*') ? 'nav-link-active' : '' }}">{{ __('ui.movies') }}</a>
                    <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.*') ? 'nav-link-active' : '' }}">{{ __('ui.favorites') }}</a>
                </nav>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('locale.update') }}" class="hidden sm:block">
                        @csrf
                        <label class="sr-only" for="locale">{{ __('ui.language') }}</label>
                        <select id="locale" name="locale" onchange="this.form.submit()" class="select-control select-small">
                            <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>EN</option>
                            <option value="id" {{ app()->getLocale() === 'id' ? 'selected' : '' }}>ID</option>
                        </select>
                    </form>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="icon-button" title="{{ __('ui.sign_out') }}" aria-label="{{ __('ui.sign_out') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path d="M10 17l5-5-5-5M15 12H3m8-7V3h10v18H11v-2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>
    @endauth

    <main class="relative z-10">
        @if (session('status'))
            <div class="site-shell pt-6">
                <div class="notice notice-success" role="status">{{ session('status') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
