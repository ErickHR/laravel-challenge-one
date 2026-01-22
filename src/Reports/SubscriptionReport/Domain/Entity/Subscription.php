<?php

namespace Src\Reports\SubscriptionReport\Domain\Entity;

class Subscription
{
    private $id;
    private $fullName;
    private $document;
    private $email;
    private $phone;

    public function __construct() {}

    public function getId()
    {
        return $this->id;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function setDocument($document)
    {
        $this->document = $document;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}
