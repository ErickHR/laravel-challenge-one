<?php

namespace Src\Reports\SubscriptionReport\Domain\Entity;

use App\Enums\StatusProcessReport;

class RegisterDownload
{

    private $id;
    private $fileName;
    private $filePath;
    private $status;

    public function __construct() {}

    public function markAsProcessing(): void
    {
        $this->status = StatusProcessReport::PROCESSING;
    }

    public function markAsPending(): void
    {
        $this->status = StatusProcessReport::PENDING;
    }

    public function markAsCompleted(): void
    {
        $this->status = StatusProcessReport::COMPLETED;
    }

    public function markAsFailed(): void
    {
        $this->status = StatusProcessReport::FAILED;
    }

    public function isCompleted(): bool
    {
        return $this->status === StatusProcessReport::COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === StatusProcessReport::PENDING;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
}
