<?php

namespace Tests\Unit;

use App\Services\OmdbService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class OmdbServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_search_maps_omdb_results()
    {
        Cache::flush();
        config(['services.omdb.key' => 'test-key']);
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->once()->with('', Mockery::on(function ($options) {
            return $options['query']['s'] === 'Dune'
                && $options['query']['type'] === 'movie'
                && $options['query']['y'] === '2024'
                && $options['query']['page'] === 1
                && $options['query']['apikey'] === 'test-key';
        }))->andReturn(new Response(200, [], json_encode([
            'Response' => 'True',
            'totalResults' => '1',
            'Search' => [[
                'imdbID' => 'tt15239678',
                'Title' => 'Dune: Part Two',
                'Year' => '2024',
                'Type' => 'movie',
                'Poster' => 'N/A',
            ]],
        ])));

        $result = (new OmdbService($client))->search('Dune', 'movie', '2024');

        $this->assertSame(1, $result['total']);
        $this->assertSame('tt15239678', $result['results'][0]['imdb_id']);
        $this->assertNull($result['results'][0]['poster']);
    }
}
