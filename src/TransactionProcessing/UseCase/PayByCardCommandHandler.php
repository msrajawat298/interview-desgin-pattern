<?php

namespace Skaleet\Interview\TransactionProcessing\UseCase;

use Skaleet\Interview\TransactionProcessing\Domain\AccountRegistry;
use Skaleet\Interview\TransactionProcessing\Domain\TransactionRepository;

class PayByCardCommandHandler
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private AccountRegistry       $accountRegistry,
    )
    {
    }


    public function handle(PayByCardCommand $command): void
    {

    }
}
