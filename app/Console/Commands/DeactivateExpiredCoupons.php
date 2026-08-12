<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class DeactivateExpiredCoupons extends Command
{
    protected $signature = 'lms:deactivate-expired-coupons';

    protected $description = 'Set status=false on coupons past their expiry date so checkout stops honoring them.';

    public function handle(): int
    {
        $count = Coupon::query()
            ->where('status', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => false]);

        $this->info("Deactivated {$count} expired coupon(s).");

        return self::SUCCESS;
    }
}
