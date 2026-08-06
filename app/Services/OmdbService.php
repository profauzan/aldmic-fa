<?php

namespace App\Services;

use App\Exceptions\OmdbApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

class OmdbService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search($query, $type = null, $year = null, $page = 1)
    {
        $cacheKey = 'omdb.search.'.md5(json_encode([$query, $type, $year, $page]));

        return Cache::remember($cacheKey, 10, function () use ($query, $type, $year, $page) {
            $params = [
                's' => $query,
                'page' => $page,
            ];

            if ($type) {
                $params['type'] = $type;
            }

            if ($year) {
                $params['y'] = $year;
            }

            $payload = $this->request($params);

            if ($payload['Response'] === 'False') {
                return ['results' => [], 'total' => 0];
            }

            return [
                'results' => array_map([$this, 'normalizeSearchResult'], $payload['Search']),
                'total' => (int) $payload['totalResults'],
            ];
        });
    }

    public function details($imdbId)
    {
        $cacheKey = 'omdb.details.'.$imdbId;

        return Cache::remember($cacheKey, 30, function () use ($imdbId) {
            $payload = $this->request([
                'i' => $imdbId,
                'plot' => 'full',
            ]);

            if ($payload['Response'] === 'False') {
                throw new OmdbApiException(__('movies.errors.not_found'), 404);
            }

            return $this->normalizeDetails($payload);
        });
    }

    protected function request(array $params)
    {
        $apiKey = config('services.omdb.key');

        if (! $apiKey) {
            throw new OmdbApiException(__('movies.errors.configuration'));
        }

        try {
            $response = $this->client->get('', [
                'query' => array_merge($params, ['apikey' => $apiKey]),
            ]);
        } catch (RequestException $exception) {
            throw new OmdbApiException(__('movies.errors.unavailable'));
        }

        if ($response->getStatusCode() >= 500) {
            throw new OmdbApiException(__('movies.errors.unavailable'));
        }

        $payload = json_decode((string) $response->getBody(), true);

        if (! is_array($payload) || ! array_key_exists('Response', $payload)) {
            throw new OmdbApiException(__('movies.errors.invalid_response'));
        }

        return $payload;
    }

    protected function normalizeSearchResult(array $movie)
    {
        return [
            'imdb_id' => $movie['imdbID'],
            'title' => $movie['Title'],
            'year' => $movie['Year'],
            'type' => $movie['Type'],
            'poster' => $this->poster($movie['Poster']),
        ];
    }

    protected function normalizeDetails(array $movie)
    {
        return [
            'imdb_id' => $movie['imdbID'],
            'title' => $movie['Title'],
            'year' => $movie['Year'],
            'rated' => $movie['Rated'],
            'released' => $movie['Released'],
            'runtime' => $movie['Runtime'],
            'genre' => $movie['Genre'],
            'director' => $movie['Director'],
            'writer' => $movie['Writer'],
            'actors' => $movie['Actors'],
            'plot' => $movie['Plot'],
            'language' => $movie['Language'],
            'country' => $movie['Country'],
            'awards' => $movie['Awards'],
            'poster' => $this->poster($movie['Poster']),
            'rating' => $movie['imdbRating'],
            'votes' => $movie['imdbVotes'],
            'type' => $movie['Type'],
            'box_office' => isset($movie['BoxOffice']) ? $movie['BoxOffice'] : null,
        ];
    }

    protected function poster($poster)
    {
        return $poster && $poster !== 'N/A' ? $poster : null;
    }
}
