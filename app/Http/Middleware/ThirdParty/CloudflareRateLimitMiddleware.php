<?php

namespace App\Http\Middleware\ThirdParty;

use App\Services\Cloudflare\CloudflareService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CloudflareRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $key = 'ip-addr:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, $perMinute = 5)) {
            $seconds = RateLimiter::availableIn($key);

            // panggil cloudflare service
            $cloudflare = new CloudflareService();
            $cloudflare->ipAccessRule($request->ip(), "block", "Bruteforce detected");

            return abort(403, 'indicated bruteforce');
        }

        RateLimiter::hit($key);
        return $next($request);
    }
}
