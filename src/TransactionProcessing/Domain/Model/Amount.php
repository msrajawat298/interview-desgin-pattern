<?php

namespace Skaleet\Interview\TransactionProcessing\Domain\Model;

class Amount
{
    public int $value; // Amount in cents
    public string $currency;

    public function __construct(int $value, string $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(int $amount): void
    {
        $this->value += $amount;
    }

    public function subtract(int $amount): void
    {
        if ($this->value < $amount) {
            throw new \DomainException('Amount exceeds current balance.');
        }
        $this->value -= $amount;
    }
}