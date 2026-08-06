<?php

namespace App\Http\Controllers;

use App\Exceptions\OmdbApiException;
use App\Services\OmdbService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $omdb;

    public function __construct(OmdbService $omdb)
    {
        $this->omdb = $omdb;
    }

    public function index(Request $request)
    {
        $filters = $this->filters($request, false);
        $data = ['results' => [], 'total' => 0];
        $error = null;
        $initialQuery = $filters['q'] ?: trim(config('services.omdb.default_query', 'popular'));
        $initialType = $filters['type'] ?: (! $filters['q'] ? config('services.omdb.default_type', 'movie') : null);

        if ($initialQuery) {
            try {
                $data = $this->omdb->search($initialQuery, $initialType, $filters['year']);
            } catch (OmdbApiException $exception) {
                $error = $exception->getMessage();
            }
        }

        return view('movies.index', [
            'movies' => $data['results'],
            'total' => $data['total'],
            'filters' => $filters,
            'initialQuery' => $initialQuery,
            'initialType' => $initialType,
            'favoriteIds' => auth()->user()->favorites()->pluck('imdb_id')->all(),
            'error' => $error,
        ]);
    }

    public function search(Request $request)
    {
        $filters = $this->filters($request, true);

        try {
            $data = $this->omdb->search($filters['q'], $filters['type'], $filters['year'], $filters['page']);
        } catch (OmdbApiException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->statusCode());
        }

        return response()->json([
            'movies' => $data['results'],
            'total' => $data['total'],
            'page' => $filters['page'],
            'has_more' => ($filters['page'] * 10) < $data['total'],
        ]);
    }

    public function show($imdbId)
    {
        abort_unless(preg_match('/^tt\d+$/', $imdbId), 404);

        try {
            $movie = $this->omdb->details($imdbId);
        } catch (OmdbApiException $exception) {
            abort($exception->statusCode(), $exception->getMessage());
        }

        return view('movies.show', [
            'movie' => $movie,
            'isFavorite' => auth()->user()->favorites()->where('imdb_id', $imdbId)->exists(),
        ]);
    }

    protected function filters(Request $request, $requireQuery)
    {
        $rules = [
            'q' => ($requireQuery ? 'required|' : 'nullable|').'string|max:100',
            'type' => 'nullable|in:movie,series,episode',
            'year' => 'nullable|digits:4',
            'page' => 'nullable|integer|min:1|max:100',
        ];

        $data = $request->validate($rules);
        $data['q'] = isset($data['q']) ? trim($data['q']) : null;
        $data['type'] = isset($data['type']) ? $data['type'] : null;
        $data['year'] = isset($data['year']) ? $data['year'] : null;
        $data['page'] = isset($data['page']) ? (int) $data['page'] : 1;

        return $data;
    }
}
