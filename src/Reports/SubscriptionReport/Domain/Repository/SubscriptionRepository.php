<?php

namespace Src\Reports\SubscriptionReport\Domain\Repository;

interface SubscriptionRepository
{
    public function byIds(array $ids);
}
