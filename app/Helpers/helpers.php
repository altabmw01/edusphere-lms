<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Fetch a site setting by key, falling back to the given default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('money')) {
    /**
     * Format a numeric amount using the configured currency symbol.
     */
    function money(float|int|string $amount): string
    {
        return config('lms.currency_symbol', '৳') . number_format((float) $amount, 2);
    }
}

if (! function_exists('star_rating')) {
    /**
     * Render a Bootstrap Icons star rating string for Blade views.
     */
    function star_rating(float $rating, int $max = 5): string
    {
        $html = '';
        for ($i = 1; $i <= $max; $i++) {
            if ($rating >= $i) {
                $html .= '<i class="bi bi-star-fill"></i>';
            } elseif ($rating >= $i - 0.5) {
                $html .= '<i class="bi bi-star-half"></i>';
            } else {
                $html .= '<i class="bi bi-star"></i>';
            }
        }

        return $html;
    }
}

if (! function_exists('duration_for_humans')) {
    /**
     * Convert a minute count into an "Xh Ym" readable string.
     */
    function duration_for_humans(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        }

        return $hours > 0 ? "{$hours}h" : "{$mins}m";
    }
}

if (! function_exists('cart_count')) {
    /**
     * Number of items in the current guest/user cart, for the nav badge.
     */
    function cart_count(): int
    {
        return app(\App\Services\CartService::class)->count();
    }
}
