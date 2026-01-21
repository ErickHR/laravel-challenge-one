<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;

use Src\Reports\SubscriptionReport\Domain\Repository\SubscriptionRepository;

class SubscriptionImpl implements SubscriptionRepository
{
    public function byIds($ids)
    {
        return DB::table('subscriptions')
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
    }
}
