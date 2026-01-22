<?php

namespace Src\Reports\SubscriptionReport\Application\Mapper;

interface ReportRowMapper
{
    public function mapCollection(object $subscriptionReport, object $subscription, iterable $relatedData): \Generator;
    public function map(object $subscriptionReport,  object $subscription,  object $debt): \Generator;
}
