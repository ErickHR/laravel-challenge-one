<?php

namespace Src\Reports\SubscriptionReport\Domain\Entity;

class ReportCreditCard
{

    private $id;
    private $subscriptionReportId;
    private $bank;
    private $line;
    private $used;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getSubscriptionReportId()
    {
        return $this->subscriptionReportId;
    }

    public function setSubscriptionReportId($subscriptionReportId)
    {
        $this->subscriptionReportId = $subscriptionReportId;
    }

    public function getBank()
    {
        return $this->bank;
    }

    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function setLine($line)
    {
        $this->line = $line;
    }

    public function getUsed()
    {
        return $this->used;
    }

    public function setUsed($used)
    {
        $this->used = $used;
    }
}
