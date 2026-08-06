<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate(['locale' => 'required|in:en,id']);
        $request->session()->put('locale', $data['locale']);

        return back();
    }
}
