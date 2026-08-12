<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use Illuminate\Console\Command;

class PruneStaleCarts extends Command
{
    protected $signature = 'lms:prune-stale-carts';

    protected $description = 'Remove guest (session-based) cart items older than 30 days.';

    public function handle(): int
    {
        $count = CartItem::query()
            ->whereNull('user_id')
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Pruned {$count} stale guest cart item(s).");

        return self::SUCCESS;
    }
}
