<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Model;

class Account
{
    public string $number;
    public Amount $balance;

    public function __construct(string $number, Amount $balance)
    {
        $this->number = $number;
        $this->balance = $balance;
    }

    /**
     * Debit the account by a certain amount.
     *
     * @param int $amount The amount to debit, in cents.
     */
    public function debit(int $amount): void
    {
        if ($this->balance->getValue() < $amount) {
            throw new \DomainException('Insufficient balance.');
        }
        $this->balance->subtract($amount);
    }

    /**
     * Credit the account by a certain amount.
     *
     * @param int $amount The amount to credit, in cents.
     */
    public function credit(int $amount): void
    {
        $this->balance->add($amount);
    }

    /**
     * Get the balance of the account.
     *
     * @return int The balance in cents.
     */
    public function getBalance(): int
    {
        return $this->balance->getValue();
    }

    /**
     * Get the currency of the account.
     *
     * @return string The currency (e.g., "EUR", "USD").
     */
    public function getCurrency(): string
    {
        return $this->balance->getCurrency();
    }
}
