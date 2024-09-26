<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Exception;

use Exception;

class InsufficientFundsException extends Exception
{
    public function __construct(string $amount)
    {
        parent::__construct("Client account has insufficient funds. $amount");
    }
}
