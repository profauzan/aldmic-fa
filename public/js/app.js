(function () {
    'use strict';

    var movieApp = document.querySelector('[data-movie-app]');
    var favoritesPage = document.querySelector('[data-favorites-page]');
    var toast = document.getElementById('toast');
    var toastTimer;
    var copy = document.body.dataset;

    function config(element) {
        var root = element.closest('[data-movie-app], [data-favorites-page]');

        return {
            csrf: element.dataset.csrf || (root && root.dataset.csrf) || document.querySelector('meta[name="csrf-token"]').content,
            favoritesUrl: element.dataset.favoriteUrl || (root && root.dataset.favoritesUrl),
            deleteUrl: element.dataset.deleteUrl || (root && root.dataset.deleteUrl),
        };
    }

    function notify(message) {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.add('is-visible');
        window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(function () { toast.classList.remove('is-visible'); }, 2600);
    }

    function setVisible(element, visible) {
        if (!element) return;
        element.classList.toggle('hidden', !visible);
    }

    function setFavoriteState(button, state) {
        var label = button.querySelector('[data-favorite-label]');
        var icon = button.querySelector('svg');
        var isFavorite = state === true;
        button.dataset.favoriteState = isFavorite ? 'true' : 'false';
        button.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
        button.setAttribute('title', isFavorite ? copy.removeFavorite : copy.addFavorite);
        button.classList.toggle('is-favorite', isFavorite);
        if (icon) icon.setAttribute('fill', isFavorite ? 'currentColor' : 'none');
        if (label) label.textContent = isFavorite ? copy.saved : copy.addFavorite;
    }

    function cardData(button) {
        var card = button.closest('[data-movie-card]');
        return {
            imdb_id: button.dataset.favoriteId,
            title: button.dataset.favoriteTitle || (card && card.dataset.title),
            year: button.dataset.favoriteYear || (card && card.dataset.year),
            type: button.dataset.favoriteType || (card && card.dataset.type),
            poster: button.dataset.favoritePoster || (card && card.dataset.poster) || '',
        };
    }

    function removeFavoriteCard(button) {
        var card = button.closest('[data-movie-card]');
        if (!card) return;
        card.style.transition = 'opacity 220ms ease, transform 220ms ease';
        card.style.opacity = '0';
        card.style.transform = 'translateY(8px) scale(.98)';
        window.setTimeout(function () { card.remove(); }, 230);
    }

    function toggleFavorite(button) {
        var settings = config(button);
        var movie = cardData(button);
        var active = button.dataset.favoriteState === 'true';
        var root = button.closest('[data-favorites-page]');

        if (active && root && root.dataset.confirm && !window.confirm(root.dataset.confirm)) return;

        button.disabled = true;
        var request = active
            ? fetch(settings.deleteUrl + '/' + encodeURIComponent(movie.imdb_id), {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': settings.csrf },
            })
            : fetch(settings.favoritesUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': settings.csrf },
                body: JSON.stringify(movie),
            });

        request.then(function (response) {
            if (!response.ok) throw new Error('Request failed');
            return response.json();
        }).then(function () {
            setFavoriteState(button, !active);
            if (active && root) removeFavoriteCard(button);
            notify(active ? copy.favoriteRemoved : copy.favoriteAdded);
        }).catch(function () {
            notify(copy.serviceError);
        }).finally(function () { button.disabled = false; });
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-favorite]');
        if (button) {
            event.preventDefault();
            toggleFavorite(button);
        }
    });

    function lazyLoadImages(scope) {
        var images = (scope || document).querySelectorAll('img[data-src]');
        if (!('IntersectionObserver' in window)) {
            Array.prototype.forEach.call(images, function (image) { image.src = image.dataset.src; });
            return;
        }
        var observer = new IntersectionObserver(function (entries, imageObserver) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var image = entry.target;
                image.src = image.dataset.src;
                image.removeAttribute('data-src');
                imageObserver.unobserve(image);
            });
        }, { rootMargin: '160px 0px' });
        Array.prototype.forEach.call(images, function (image) { observer.observe(image); });
    }

    function createMovieCard(movie, detailUrl) {
        var article = document.createElement('article');
        article.className = 'movie-card reveal';
        article.dataset.movieCard = '';
        article.dataset.title = movie.title;
        article.dataset.year = movie.year;
        article.dataset.type = movie.type;
        article.dataset.poster = movie.poster || '';

        var link = document.createElement('a');
        link.className = 'poster-frame group';
        link.href = detailUrl + '/' + encodeURIComponent(movie.imdb_id);
        var poster;
        if (movie.poster) {
            poster = document.createElement('img');
            poster.className = 'poster-image';
            poster.alt = movie.title + ' poster';
            poster.loading = 'lazy';
            poster.dataset.src = movie.poster;
        } else {
            poster = document.createElement('div');
            poster.className = 'poster-empty';
            var emptyLabel = document.createElement('span');
            emptyLabel.textContent = copy.posterUnavailable;
            poster.appendChild(emptyLabel);
        }
        link.appendChild(poster);
        var overlay = document.createElement('span');
        overlay.className = 'poster-overlay';
        overlay.textContent = copy.viewDetails + ' ->';
        link.appendChild(overlay);
        article.appendChild(link);

        var info = document.createElement('div');
        info.className = 'pt-4';
        var row = document.createElement('div');
        row.className = 'flex items-start justify-between gap-3';
        var text = document.createElement('div');
        text.className = 'min-w-0';
        var title = document.createElement('p');
        title.className = 'truncate font-display text-base font-bold text-paper';
        title.textContent = movie.title;
        var meta = document.createElement('p');
        meta.className = 'mt-1 font-mono text-[10px] uppercase tracking-[0.16em] text-muted';
        meta.textContent = movie.year + ' / ' + movie.type;
        text.appendChild(title);
        text.appendChild(meta);
        row.appendChild(text);

        var favorite = document.createElement('button');
        favorite.type = 'button';
        favorite.className = 'favorite-button';
        favorite.dataset.favorite = '';
        favorite.dataset.favoriteId = movie.imdb_id;
        favorite.dataset.favoriteState = 'false';
        favorite.setAttribute('aria-label', copy.addFavorite);
        favorite.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="h-4 w-4"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        row.appendChild(favorite);
        info.appendChild(row);
        article.appendChild(info);
        return article;
    }

    if (movieApp) {
        var form = document.querySelector('[data-search-form]');
        var grid = movieApp.querySelector('[data-movie-grid]');
        var empty = movieApp.querySelector('[data-empty]');
        var start = movieApp.querySelector('[data-start]');
        var sentinel = movieApp.querySelector('[data-sentinel]');
        var more = movieApp.querySelector('[data-load-more]');
        var loadingLabel = movieApp.querySelector('[data-loading-label]');
        var resultLabel = movieApp.querySelector('[data-result-label]');
        var state = { page: Number(movieApp.dataset.page || 1), total: Number(movieApp.dataset.total || 0), loading: false, hasMore: Number(movieApp.dataset.total || 0) > grid.children.length && Boolean(movieApp.dataset.query) };

        function setLoading(value) {
            state.loading = value;
            setVisible(more, value);
            if (loadingLabel) loadingLabel.classList.toggle('flex', value);
        }

        function runSearch(page, append) {
            if (state.loading) return;
            var params = new URLSearchParams(new FormData(form));
            if (!params.get('q') && movieApp.dataset.query) params.set('q', movieApp.dataset.query);
            params.set('page', page);
            setLoading(true);
            fetch(movieApp.dataset.searchUrl + '?' + params.toString(), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (response) { if (!response.ok) throw new Error('Search failed'); return response.json(); })
                .then(function (payload) {
                    if (!append) grid.innerHTML = '';
                    payload.movies.forEach(function (movie, index) {
                        var card = createMovieCard(movie, movieApp.dataset.detailUrl);
                        card.style.animationDelay = (Math.min(index, 7) * 45) + 'ms';
                        grid.appendChild(card);
                    });
                    state.page = payload.page;
                    state.total = payload.total;
                    state.hasMore = payload.has_more;
                    movieApp.dataset.query = params.get('q');
                    setVisible(grid, payload.movies.length > 0 || append && grid.children.length > 0);
                    setVisible(empty, payload.movies.length === 0 && !append);
                    setVisible(start, false);
                    if (resultLabel) resultLabel.textContent = payload.total + ' ' + copy.results;
                    lazyLoadImages(grid);
                }).catch(function () {
                    if (!append) { setVisible(grid, false); setVisible(empty, false); }
                    notify(copy.serviceError);
                }).finally(function () { setLoading(false); });
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            state.page = 1;
            runSearch(1, false);
        });

        if ('IntersectionObserver' in window && sentinel) {
            var sentinelObserver = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting && state.hasMore && !state.loading) runSearch(state.page + 1, true);
            }, { rootMargin: '400px 0px' });
            sentinelObserver.observe(sentinel);
        }
        lazyLoadImages(movieApp);
    } else {
        lazyLoadImages(favoritesPage || document);
    }
}());
