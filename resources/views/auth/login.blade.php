@extends('layouts.app', ['title' => __('ui.sign_in')])

@section('content')
<div class="login-stage site-shell">
    <div class="login-art" aria-hidden="true">
        <div class="orb orb-one"></div>
        <div class="orb orb-two"></div>
        <div class="art-grid"></div>
        <div class="film-reel">
            <span></span><span></span><span></span><span></span><span></span><span></span>
        </div>
        <div class="art-caption"><span>ARCHIVE / 01</span><span>CURATED MOTION</span></div>
    </div>
    <section class="login-panel animate-rise">
        <div class="mb-10 flex items-center justify-between">
            <a href="{{ route('login') }}" class="flex items-center gap-3">
                <span class="brand-mark">FC</span>
                <span class="font-display text-lg font-extrabold">{{ __('ui.app_name') }}</span>
            </a>
            <form method="POST" action="{{ route('locale.update') }}">
                @csrf
                <label class="sr-only" for="login-locale">{{ __('ui.language') }}</label>
                <select id="login-locale" name="locale" onchange="this.form.submit()" class="select-control select-small">
                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>EN</option>
                    <option value="id" {{ app()->getLocale() === 'id' ? 'selected' : '' }}>ID</option>
                </select>
            </form>
        </div>
        <p class="section-kicker">{{ __('ui.eyebrow') }}</p>
        <h1 class="display-title mt-3">{{ __('ui.login_title') }}</h1>
        <p class="mt-5 max-w-md text-sm leading-7 text-muted">{{ __('ui.login_copy') }}</p>

        <form method="POST" action="{{ route('login.attempt') }}" class="mt-10 space-y-5">
            @csrf
            <div>
                <label for="username" class="field-label">{{ __('ui.username') }}</label>
                <input id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" class="text-control @error('username') border-red-400 @enderror">
                @error('username')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="field-label">{{ __('ui.password') }}</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="text-control @error('password') border-red-400 @enderror">
                @error('password')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="button-primary w-full">{{ __('ui.sign_in') }} <span aria-hidden="true">↗</span></button>
        </form>
    </section>
</div>
@endsection
