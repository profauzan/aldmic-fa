# Movie Advisor Technical Test Plan

## 1. Objective

Build a movie advisor application using Laravel 5.8 and the OMDb API with the following features:

- Login using the required credentials.
- Movie listing and search.
- Filtering by type and year.
- Movie detail page.
- Infinite scroll on the movie list.
- Lazy loading for movie posters.
- Add and remove favorites.
- Favorite movie list.
- English and Indonesian localization.
- Empty states for missing data.
- Railway deployment support.
- Complete README with libraries, architecture, screenshots, and demo URL.

## 2. Technical Decisions

### Backend

- Laravel 5.8, the final major release in the Laravel 5 series.
- A PHP runtime selected for Laravel 5.8 and dependency compatibility.
- MVC architecture with a Service Layer for OMDb communication.
- PostgreSQL as the database.
- OMDb API key loaded from environment variables.
- The required user created through a database seeder.

Laravel 5.8 is no longer maintained. Because the requirement explicitly asks for Laravel 5, dependencies will be selected for compatibility and locked through dependency lock files rather than forcing newer versions that may not work with the framework.

### Frontend

- Laravel Blade for server-rendered views.
- Tailwind CSS for responsive styling.
- Tailwind CSS built with its CLI rather than loaded from a CDN.
- Fetch API for asynchronous requests.
- IntersectionObserver for infinite scroll and lazy loading.
- No jQuery because it is not required.
- No Vite because Laravel 5.8 does not use Vite natively.

Blade is not explicitly required by the test, but it is the safest view layer for this implementation because:

- It is native to Laravel.
- It clearly demonstrates Laravel usage.
- It avoids unnecessary SPA framework complexity.
- It keeps Railway deployment simpler.
- It still supports modern JavaScript for infinite scroll and favorite actions.

## 3. Libraries and Tooling

- Laravel 5.8.
- The latest stable Guzzle release compatible with Laravel 5.8.
- The latest PHPUnit release compatible with Laravel 5.8.
- The latest stable Tailwind CSS release compatible with the build environment.
- PostgreSQL.
- Composer.
- Node.js and the Tailwind CLI for asset compilation.
- Browser-native Fetch API and IntersectionObserver.

Unnecessary frontend dependencies will not be added when browser APIs already satisfy the requirement. All dependencies will be locked through `composer.lock` and `package-lock.json` where applicable.

## 4. Application Architecture

The planned structure is:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── MovieController.php
│   │   ├── FavoriteController.php
│   │   └── LocaleController.php
│   └── Middleware/
│       └── SetLocale.php
├── Models/
│   ├── User.php
│   └── Favorite.php
└── Services/
    └── OmdbService.php

resources/
├── views/
│   ├── layouts/
│   ├── auth/
│   ├── movies/
│   └── favorites/
├── lang/
│   ├── en/
│   └── id/
└── css/

database/
├── migrations/
└── seeds/

tests/
├── Feature/
└── Unit/
```

Layer responsibilities:

- Controllers handle requests, authorization, validation, and responses.
- `OmdbService` handles requests, response mapping, caching, and OMDb errors.
- Models handle relationships and database operations.
- Blade handles HTML rendering.
- JavaScript handles client interactions without full page reloads.
- Translation files contain application static text.

## 5. Authentication

Required credentials:

- Username: `aldmic`
- Password: `123abc123`

Implementation:

- The user is created through a seeder.
- The password is stored using Laravel hashing.
- Login uses session authentication.
- The session is regenerated after successful login.
- Movie and favorite pages are protected by the `auth` middleware.
- Failed login attempts display an error message.
- Logout invalidates the authenticated session.
- The login form uses CSRF protection.
- The login route uses throttling to reduce brute-force attempts.

## 6. Database

### `users` Table

Use Laravel's standard user table structure.

### `favorites` Table

Columns:

- `id`
- `user_id`
- `imdb_id`
- `title`
- `year`
- `type`
- `poster`
- `created_at`
- `updated_at`

Constraints:

- Foreign key to `users`.
- Composite unique index on `user_id` and `imdb_id`.
- Users can only read and delete their own favorites.

The minimum movie data is stored so the favorite page does not depend on an additional API request. Full details can still be fetched from OMDb when the movie is opened.

## 7. Routes

### Public Routes

- `GET /login`
- `POST /login`
- `POST /locale`

### Authenticated Routes

- `POST /logout`
- `GET /movies`
- `GET /movies/search`
- `GET /movies/{imdbId}`
- `GET /favorites`
- `POST /favorites`
- `DELETE /favorites/{imdbId}`

The search endpoint returns HTML for normal requests and JSON for infinite scroll requests.

## 8. OMDb API Integration

Configuration will be prepared in `.env.example`:

```env
OMDB_API_KEY=
OMDB_BASE_URL=https://www.omdbapi.com/
```

### Search

Parameters:

- `s`: movie title, required.
- `type`: `movie`, `series`, or `episode`.
- `y`: four-digit year.
- `page`: page number.
- `apikey`: API key.

The initial movie catalog uses `MOVIE_DEFAULT_QUERY` and `MOVIE_DEFAULT_TYPE` so the homepage can show movies before the user performs a search. The defaults are `popular` and `movie` and can be changed through environment configuration.

### Detail

Parameters:

- `i`: IMDb ID.
- `plot=full`.
- `apikey`: API key.

### Error Handling

The application handles:

- Invalid API key.
- Movie not found.
- API timeout.
- API rate limit.
- Invalid API response.
- Poster value of `N/A`.
- Empty search results.

Laravel Cache may be used for short-lived search and detail responses to reduce repeated API requests and OMDb quota usage.

## 9. Infinite Scroll

Infinite scroll flow:

1. The user searches with a title and optional filters.
2. The first page is rendered through Blade.
3. JavaScript reads the page and total-result metadata.
4. When the sentinel element becomes visible, the next page is requested through Fetch API.
5. New movies are appended to the grid without a page reload.
6. No additional request is made while the previous request is still pending.
7. Infinite scroll stops when all results are displayed or the API returns an error.
8. A new search resets the page, result count, and loading state.

Frontend state:

- Search query.
- Selected type.
- Selected year.
- Current page.
- Total result count.
- Loading status.
- Whether more data is available.

## 10. Poster Lazy Loading

Implementation:

- Native `loading="lazy"` attribute.
- `data-src` for IntersectionObserver fallback behavior.
- Placeholder when a poster is unavailable.
- Fallback image when a poster URL fails.

Movie cards continue to show the title and metadata when no poster is available.

## 11. Visual Design Direction

The interface will use an editorial cinematic style that feels like a premium movie catalog rather than a generic admin dashboard.

Visual principles:

- Dark navy background with slightly lighter surfaces to create depth.
- Amber accents for primary actions, ratings, favorite status, and interactive elements.
- Strong sans-serif typography for navigation and metadata.
- Large, high-contrast movie titles with clear hierarchy.
- Movie posters as the primary visual focus in cards and detail pages.
- Subtle borders, consistent radius, and informative hover states.
- Spacious search area as the main interaction focal point.
- Large poster and structured information layout on the detail page.
- Empty states with visuals consistent with the cinematic theme.
- Skeleton or placeholder loading states to prevent layout shifts.
- Responsive grid that adapts from mobile to desktop.
- Sufficient contrast and visible focus states for keyboard users.

The target experience is:

- Desktop feels like a premium streaming catalog.
- Mobile remains comfortable with search controls that do not feel crowded.
- Favorite actions are clear without competing with movie information.
- Motion and hover effects feel polished without distracting the user.

## 12. Motion and Animation

Animations are required as part of the visual polish, but they must remain subtle and purposeful.

Planned motion:

- Login panel fades and slides into position on initial load.
- Page content uses a short fade-up transition when entering.
- Movie cards reveal with a staggered fade-up effect after results load.
- Poster images fade from the placeholder into the loaded image.
- Movie cards lift slightly and increase shadow depth on hover.
- Favorite buttons animate between outline and active states.
- Favorite actions display a short scale or pulse confirmation.
- Favorite removal uses a collapse and fade-out transition.
- Loading states use a soft shimmer skeleton animation.
- Infinite-scroll content enters with a short fade-up transition.
- Detail-page poster and metadata appear with coordinated transitions.
- Toast notifications slide in and out without blocking content.
- Mobile menu and filter panels use smooth height and opacity transitions.

Motion rules:

- Keep normal transitions between 150ms and 300ms.
- Avoid animations that delay navigation or content access.
- Do not animate every element continuously.
- Respect `prefers-reduced-motion: reduce` by disabling non-essential transitions.
- Preserve keyboard focus visibility during animated state changes.
- Ensure animations do not cause layout shift or horizontal overflow.
- Use CSS transitions and keyframes before adding a JavaScript animation library.

## 13. Application Pages

### Login

- Username input.
- Password input.
- Login button.
- Validation messages.
- Invalid-credential message.
- Language switcher.
- Introductory animated visual treatment.

### Movie List

- Application header.
- Logout button.
- Language switcher.
- Search input.
- Type filter.
- Year filter.
- Search button.
- Responsive movie card grid.
- Add or remove favorite button.
- Loading indicator.
- Infinite scroll sentinel.
- Empty state.
- Error state.
- Card entrance and hover animations.

### Movie Detail

- Movie poster.
- Title.
- Year.
- Genre.
- Runtime.
- Rating.
- Director.
- Actors.
- Plot.
- Additional OMDb information.
- Favorite button.
- Back-to-list link.
- Coordinated poster and information entrance animation.

### Favorite List

- Favorite movie list.
- Movie detail link.
- Remove favorite button.
- Delete confirmation.
- Success or failure feedback.
- Empty state when no favorite exists.
- Removal animation before the card leaves the grid.

## 14. Localization

The default language is English (`en`). Available languages:

- English: `en`.
- Indonesian: `id`.

Localization applies only to static application text:

- Form labels.
- Buttons.
- Navigation.
- Login messages.
- Error messages.
- Empty states.
- Loading states.
- Favorite messages.
- Filters.
- Validation messages.

Data returned by OMDb will not be translated.

The locale is stored in the session and applied through the `SetLocale` middleware.

## 15. Security

- Passwords use hashing.
- Private routes use the `auth` middleware.
- CSRF protection is applied to forms and mutation requests.
- Sessions are regenerated after login.
- All user input is validated.
- Blade escaping is used for rendered output.
- The API key is loaded only from environment variables.
- Favorite access is restricted by `user_id`.
- The login route is throttled.
- IMDb IDs are validated before use.
- Internal error details are not exposed to users.

## 16. Empty States

Empty states will be provided for:

- No search results.
- No saved favorites.
- Missing posters.
- Missing movie details.
- Unavailable API.
- Filters returning no data.

Each empty state will have a clear message and a relevant next action when appropriate.

## 17. Testing

### Feature Tests

- A user can open the login page.
- Valid credentials successfully log in.
- Invalid credentials show an error.
- Guests cannot open the movie list.
- Guests cannot open movie details.
- Guests cannot open the favorite page.
- An authenticated user can log out.
- An authenticated user can add a favorite.
- The same favorite cannot be duplicated.
- A user can only delete their own favorite.
- A user can change the application language.

### Unit Tests

- `OmdbService` builds requests with the correct parameters.
- Successful responses are mapped correctly.
- OMDb error responses are handled correctly.
- A poster value of `N/A` is treated as unavailable.
- Search cache keys are consistent.

OMDb requests will be mocked so tests do not depend on the external API.

## 18. README.md

The README must include:

- Application name and description.
- Feature list.
- Application screenshots.
- Libraries used.
- MVC and Service Layer architecture explanation.
- PHP, Composer, Node.js, and PostgreSQL requirements.
- Installation instructions.
- `.env` configuration instructions.
- Migration and seeder commands.
- Asset build commands.
- Test commands.
- Demo credentials.
- Railway deployment instructions.
- Demo URL after deployment.
- A note that the API key must not be committed.

## 19. Railway Deployment

Deployment will use Docker so the Laravel 5.8 runtime can be controlled explicitly.

Checklist:

- Provide a `Dockerfile`.
- Provide the web process configuration.
- Set `APP_ENV=production`.
- Set `APP_DEBUG=false`.
- Set `APP_URL` to the Railway application URL.
- Connect the Railway PostgreSQL database.
- Add `OMDB_API_KEY` through Railway Variables.
- Run migrations and the user seeder.
- Build assets before starting the application.
- Confirm Laravel storage and file permissions.
- Test login, search, detail, favorites, localization, and animations.

The demo URL will be added after the application is deployed by the project owner.

## 20. Definition of Done

The implementation is complete when:

- The project runs on Laravel 5.8.
- The database uses PostgreSQL.
- Login uses the required credentials.
- Private pages cannot be opened without authentication.
- OMDb search works.
- Movie filters work.
- Movie details work.
- Infinite scroll works.
- Poster lazy loading works.
- Favorite movies can be added and removed.
- The favorite page includes an empty state.
- English and Indonesian languages are available.
- Tailwind CSS is used for responsive UI.
- Animations are implemented and respect reduced-motion preferences.
- Main tests pass.
- README is complete.
- Screenshots are available.
- Railway deployment instructions are available.
- The API key is not committed to the repository.
- The demo URL can be added after deployment.
