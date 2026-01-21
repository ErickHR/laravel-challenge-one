<?php

namespace Src\Reports\SubscriptionReport\Domain\Repository;

interface SubscriptionReportRepository
{
    public function getByLastId($lastId, $filters);
}
