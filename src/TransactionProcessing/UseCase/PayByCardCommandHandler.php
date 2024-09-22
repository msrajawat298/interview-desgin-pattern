<?php

namespace Skaleet\Interview\TransactionProcessing\UseCase;

use Skaleet\Interview\TransactionProcessing\Domain\AccountRegistry;
use Skaleet\Interview\TransactionProcessing\Domain\TransactionRepository;
use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;
use Skaleet\Interview\TransactionProcessing\Domain\Model\AccountingEntry;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Amount;
use DateTimeImmutable;
use Skaleet\Interview\TransactionProcessing\Domain\Exception\AccountDoesNotExistException;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Account;
use Skaleet\Interview\TransactionProcessing\Util\TransactionDisplay;

class PayByCardCommandHandler
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private AccountRegistry       $accountRegistry,
    )
    {
    }


    /**
     * @throws AccountDoesNotExistException
     * @throws \Exception
     */
    public function handle(PayByCardCommand $command): void
    {
        $amount = $command->amount;
        $currency = $command->currency;

        $this->validateTransactionAmount($amount);
        list($clientAccount, $merchantAccount) = $this->loadAccounts($command);

        $this->validateCurrencies($clientAccount, $merchantAccount, $currency);

        $this->processTransaction($clientAccount, $merchantAccount, $amount, $currency);
    }

    /**
     * @throws \Exception
     */
    private function validateTransactionAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new \Exception("Transaction amount must be more than 1.");
        }
    }

    /**
     * @throws AccountDoesNotExistException
     */
    private function loadAccounts(PayByCardCommand $command): array
    {
        $clientAccount = $this->accountRegistry->loadByNumber($command->clientAccountNumber);
        $merchantAccount = $this->accountRegistry->loadByNumber($command->merchantAccountNumber);

        if (!$clientAccount) {
            throw new AccountDoesNotExistException($command->clientAccountNumber);
        }
        if (!$merchantAccount) {
            throw new AccountDoesNotExistException($command->merchantAccountNumber);
        }

        return [$clientAccount, $merchantAccount];
    }

    private function validateCurrencies(Account $clientAccount, Account $merchantAccount, string $currency): void
    {
        if ($clientAccount->balance->getCurrency() !== $currency) {
            throw new \InvalidArgumentException("Client account currency does not match the transaction currency");
        }
        if ($merchantAccount->balance->getCurrency() !== $currency) {
            throw new \InvalidArgumentException("Merchant account currency does not match the transaction currency");
        }
    }

    private function processTransaction(Account $clientAccount, Account $merchantAccount, int $amount, string $currency): void
    {
        $clientAccount->debit($amount);
        $merchantAccount->credit($amount);

        $transactionLog = new TransactionLog(
            uniqid(),
            new DateTimeImmutable(),
            [
                new AccountingEntry($clientAccount->number, new Amount(-$amount, $currency), new Amount($clientAccount->getBalance(), $currency)),
                new AccountingEntry($merchantAccount->number, new Amount($amount, $currency), new Amount($merchantAccount->getBalance(), $currency))
            ]
        );

        $this->transactionRepository->add($transactionLog);
        TransactionDisplay::displayTransaction($transactionLog);
    }
}
