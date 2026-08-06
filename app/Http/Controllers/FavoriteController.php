<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        return view('favorites.index', [
            'favorites' => auth()->user()->favorites()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'imdb_id' => 'required|regex:/^tt\d+$/|max:20',
            'title' => 'required|string|max:255',
            'year' => 'nullable|string|max:20',
            'type' => 'nullable|in:movie,series,episode',
            'poster' => 'nullable|url|max:500',
        ]);

        $favorite = auth()->user()->favorites()->updateOrCreate(
            ['imdb_id' => $data['imdb_id']],
            $data
        );

        if ($request->expectsJson()) {
            return response()->json(['favorite' => true, 'id' => $favorite->imdb_id], 201);
        }

        return back()->with('status', __('favorites.added'));
    }

    public function destroy(Request $request, $imdbId)
    {
        abort_unless(preg_match('/^tt\d+$/', $imdbId), 404);

        $deleted = auth()->user()->favorites()->where('imdb_id', $imdbId)->delete();

        if ($request->expectsJson()) {
            return response()->json(['deleted' => $deleted > 0]);
        }

        return back()->with('status', __('favorites.removed'));
    }
}
