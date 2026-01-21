<?php

namespace Src\Reports\SubscriptionReport\Domain\Entity;

class SubscriptionReport
{
    private $subscriptionId;
    private $period;

    public function __construct() {}

    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    public function getPeriod()
    {
        return $this->period;
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }
}
