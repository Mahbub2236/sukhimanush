<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers for defense-in-depth against Clickjacking, MIME sniffing, and information disclosure
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(self), geolocation=(self)');

        // Content Security Policy (allows self, Google Fonts, CDNjs FontAwesome, payment gateways, and AI endpoints)
        $csp = "default-src 'self'; "
            ."script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
            ."style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
            ."font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
            ."img-src 'self' data: blob: https:; "
            ."connect-src 'self' https:; "
            ."frame-src 'self' https://*.bkash.com https://*.sslcommerz.com; "
            ."object-src 'none'; "
            ."base-uri 'self';";

        if (config('app.env') === 'production') {
            $response->headers->set('Content-Security-Policy', $csp);
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        } elseif ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
