<?php

namespace Src\Reports\SubscriptionReport\Domain\Service\Report;

interface Closeable
{
    public function close(): void;
}
