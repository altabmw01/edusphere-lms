<?php

namespace App\Services;

use App\Models\Country;
use App\Models\District;

class ShippingService
{
    /**
     * - Bangladesh + Dhaka district  → setting('shipping_cost_dhaka')
     * - Bangladesh + any other district → setting('shipping_cost_outside_dhaka')
     * - Any other country → that Country's own shipping_cost column
     * - No country selected → 0
     */
    public function calculate(?Country $country, ?District $district): float
    {
        if (! $country) {
            return 0.0;
        }

        if ($country->isBangladesh()) {
            if ($district && $district->isDhaka()) {
                return (float) setting('shipping_cost_dhaka', 0);
            }

            return (float) setting('shipping_cost_outside_dhaka', 0);
        }

        return (float) $country->shipping_cost;
    }
}
