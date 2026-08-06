# Film Cabinet

Film Cabinet is a private movie discovery application built for the Laravel 5 technical test. It uses the OMDb API for catalog data and PostgreSQL for user favorites.

## Features

- Fixed-credential session login.
- OMDb movie, series, and episode search.
- Type and year filters.
- Infinite scroll with request locking.
- Native lazy poster loading with fallback states.
- Movie detail pages.
- Per-user favorite storage and removal.
- English and Indonesian localization.
- Responsive Blade and Tailwind CSS interface.
- Cinematic transitions, card reveals, hover states, loading motion, and reduced-motion support.
- Feature and unit tests with mocked OMDb requests.

## Stack

- PHP 7.4 runtime for Laravel 5.8 compatibility.
- Laravel 5.8.
- PostgreSQL 15.
- Guzzle 6 for OMDb HTTP requests.
- Blade templates.
- Tailwind CSS 3 CLI.
- Browser-native Fetch API and IntersectionObserver.
- Apache in Docker.

## Architecture

The application follows Laravel MVC with a small service layer:

- Controllers validate requests and coordinate responses.
- `App\Services\OmdbService` owns API requests, response normalization, caching, and external-service errors.
- Eloquent models own users and favorites.
- Blade renders the initial page and movie cards.
- `public/js/app.js` owns async search, infinite scroll, lazy loading, and favorite mutations.
- Locale middleware applies the session-selected language to every web request.

## Requirements

The recommended workflow requires:

- Docker Desktop with Docker Compose.
- An OMDb API key.

Native PHP and Node.js are not required when using Docker. For local asset development, Node.js 20 or newer is recommended.

## Configuration

Copy the example environment file:

```sh
cp .env.example .env
```

Set at least:

```env
APP_KEY=
OMDB_API_KEY=your-omdb-api-key
MOVIE_DEFAULT_QUERY=popular
MOVIE_DEFAULT_TYPE=movie
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=film_cabinet
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Never commit `.env` or the OMDb API key.

## Local Docker Setup

Start the application and PostgreSQL:

```sh
docker compose up -d --build
```

The application is available at `http://localhost:8080`.

The Compose command runs migrations and the database seeder on startup. To follow logs:

```sh
docker compose logs -f app
```

Stop the services:

```sh
docker compose down
```

Remove the local database volume as well:

```sh
docker compose down -v
```

## Demo Login

- Username: `aldmic`
- Password: `123abc123`

The password is hashed by the database seeder. It is not stored in plaintext.

## Asset Development

Install frontend dependencies and build Tailwind CSS:

```sh
npm install
npm run production
```

Watch CSS during development:

```sh
npm run watch
```

JavaScript is intentionally dependency-free and is served from `public/js/app.js`.

## Testing

Build the test image with development dependencies:

```sh
docker build --build-arg INSTALL_DEV=true -t film-cabinet:test-suite .
```

Run the full suite against an in-memory SQLite database:

```sh
docker run --rm \
  -e APP_KEY='base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=' \
  -e APP_ENV=testing \
  -e DB_CONNECTION=sqlite \
  -e DB_DATABASE=':memory:' \
  -e CACHE_DRIVER=array \
  -e SESSION_DRIVER=array \
  film-cabinet:test-suite php vendor/bin/phpunit
```

OMDb requests are mocked in unit tests. Runtime production storage remains PostgreSQL.

## Railway Deployment

Deploy using the repository `Dockerfile`:

1. Create a Railway project and add a PostgreSQL service.
2. Add the application service from this repository.
3. Set the application variables below in Railway Variables.
4. Set the PostgreSQL connection variables from the Railway database reference.
5. Deploy the service.

Required application variables:

```env
APP_NAME=Film Cabinet
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-domain.up.railway.app
APP_KEY=base64:your-generated-32-byte-key
OMDB_API_KEY=your-omdb-api-key
OMDB_BASE_URL=https://www.omdbapi.com/
DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}
```

Run the production migration and seeder from the Railway service shell if the deployment command does not run them automatically:

```sh
php artisan migrate --seed --force
```

Set `APP_DEBUG=false` in production. The API key and application key must be stored only in Railway Variables.

## Screenshots and Demo URL

Capture the login, movie catalog, movie detail, and favorites views after deploying. Add the resulting image paths and the Railway URL to this section before submitting the technical test.

## Project Layout

```text
app/Http/Controllers/     Request and response orchestration
app/Http/Middleware/      Locale handling
app/Models/               Favorite model
app/Services/              OMDb integration
database/migrations/      Users and favorites schema
database/seeds/           Required demo user
resources/views/          Blade pages and components
resources/css/             Tailwind source and motion styles
public/js/                 Browser-native interactions
tests/                     Feature and unit coverage
docker/                    Apache and container entrypoint configuration
```
