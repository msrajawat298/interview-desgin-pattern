<?php

namespace Skaleet\Interview\TransactionProcessing\Util;

use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;

class TransactionDisplay
{
    public static function displayTransaction(TransactionLog $transactionLog): void
    {
        echo "Transaction ID: " . $transactionLog->getId() . "\n";
        echo "Date: " . $transactionLog->getDate()->format('Y-m-d H:i:s') . "\n";
        echo "Accounting Entries:\n";
        foreach ($transactionLog->getAccountingEntries() as $entry) {
            echo "  Account Number: " . $entry->accountNumber . "\n";
            echo "  Amount: " . $entry->amount->getValue() . " " . $entry->amount->getCurrency() . "\n";
            echo "  New Balance: " . $entry->newBalance->getValue() . " " . $entry->newBalance->getCurrency() . "\n";
            echo PHP_EOL;
        }
        echo "-----------------------------\n";
    }
}