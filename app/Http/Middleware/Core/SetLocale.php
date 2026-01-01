<?php

namespace App\Http\Middleware\Core;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

// TODO: Add to boilerplate
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // $user = Auth::user();

        // if ($user && isset($user->preferred_language)) {
        //     App::setLocale($user->preferred_language);
        // } else {
            $locale = $request->locale ?? session('locale') ?? config('app.locale');
            App::setLocale($locale);
        // }

        return $next($request);
    }
}