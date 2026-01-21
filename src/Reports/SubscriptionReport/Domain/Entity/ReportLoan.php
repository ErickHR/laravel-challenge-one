<?php

namespace Src\Reports\SubscriptionReport\Domain\Entity;

class ReportLoan
{

    private $id;
    private $subscriptionReportId;
    private $bank;
    private $status;
    private $currency;
    private $amount;
    private $expiration_days;

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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getExpirationDays()
    {
        return $this->expiration_days;
    }

    public function setExpirationDays($expiration_days)
    {
        $this->expiration_days = $expiration_days;
    }
}
