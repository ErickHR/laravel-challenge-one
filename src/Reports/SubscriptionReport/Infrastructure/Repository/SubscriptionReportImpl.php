<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;

use Src\Reports\SubscriptionReport\Domain\Repository\SubscriptionReportRepository;

class SubscriptionReportImpl implements SubscriptionReportRepository
{
    const CHUNK_SIZE = 1000;

    public function getByLastId($lastId, $filters)
    {
        return DB::table('subscription_reports')
            ->select('id', 'subscription_id', 'created_at')
            ->whereBetween('created_at', [$filters['from'], $filters['to']])
            ->where('id', '>', $lastId)
            ->orderBy('id')
            ->limit(self::CHUNK_SIZE)
            ->get();
    }
}
