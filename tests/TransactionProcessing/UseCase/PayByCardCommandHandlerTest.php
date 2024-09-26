<?php

namespace Skaleet\Interview\Tests\TransactionProcessing\UseCase;

use PHPUnit\Framework\TestCase;
use Skaleet\Interview\TransactionProcessing\UseCase\PayByCardCommand;
use Skaleet\Interview\TransactionProcessing\UseCase\PayByCardCommandHandler;
use Skaleet\Interview\TransactionProcessing\Domain\AccountRegistry;
use Skaleet\Interview\TransactionProcessing\Domain\TransactionRepository;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Account;
use Skaleet\Interview\TransactionProcessing\Domain\Model\Amount;
use Skaleet\Interview\TransactionProcessing\Domain\Model\TransactionLog;
use Skaleet\Interview\TransactionProcessing\Domain\Exception\AccountDoesNotExistException;
use Skaleet\Interview\TransactionProcessing\Domain\Exception\InsufficientFundsException;

class PayByCardCommandHandlerTest extends TestCase
{
    private $transactionRepository;
    private $accountRegistry;
    private $payByCardCommandHandler;

    protected function setUp(): void
    {
        $this->transactionRepository = $this->createMock(TransactionRepository::class);
        $this->accountRegistry = $this->createMock(AccountRegistry::class);
        $this->payByCardCommandHandler = new PayByCardCommandHandler(
            $this->transactionRepository,
            $this->accountRegistry
        );
    }

    public function testHandleValidTransaction()
    {
        $clientAccount = $this->createMock(Account::class);
        $merchantAccount = $this->createMock(Account::class);
        $amount = 1000;
        $currency = 'EUR';

        // Initialize account number and balance
        $clientAccount->number = 'client-account';
        $clientAccount->balance = new Amount(2000, $currency);
        $merchantAccount->number = 'merchant-account';
        $merchantAccount->balance = new Amount(500, $currency);

        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', $clientAccount],
                ['merchant-account', $merchantAccount]
            ]);

        $clientAccount->expects($this->once())->method('debit')->with($amount);
        $merchantAccount->expects($this->once())->method('credit')->with($amount);

        $this->transactionRepository->expects($this->once())->method('add')
            ->with($this->isInstanceOf(TransactionLog::class));

        $this->expectOutputRegex('/Transaction ID:/');

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: $amount,
            currency: $currency
        ));
    }

    public function testHandleThrowsExceptionForInsufficientFunds()
    {
        $clientAccount = $this->createMock(Account::class);
        $merchantAccount = $this->createMock(Account::class);
        $amount = 3000;
        $currency = 'EUR';

        $clientAccount->balance = new Amount(2000, $currency);
        $merchantAccount->balance = new Amount(500, $currency);

        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', $clientAccount],
                ['merchant-account', $merchantAccount]
            ]);

        $clientAccount->method('debit')->willThrowException(new InsufficientFundsException('Insufficient funds'));

        $this->expectException(InsufficientFundsException::class);

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: $amount,
            currency: $currency
        ));
    }

    public function testHandleThrowsExceptionForNonExistentClientAccount()
    {
        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', null],
                ['merchant-account', $this->createMock(Account::class)]
            ]);

        $this->expectException(AccountDoesNotExistException::class);

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: 1000,
            currency: 'EUR'
        ));
    }

    public function testHandleThrowsExceptionForCurrencyMismatch()
    {
        $clientAccount = $this->createMock(Account::class);
        $merchantAccount = $this->createMock(Account::class);
        $amount = 1000;
        $currency = 'USD';

        $clientAccount->balance = new Amount(2000, 'EUR');
        $merchantAccount->balance = new Amount(500, 'EUR');

        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', $clientAccount],
                ['merchant-account', $merchantAccount]
            ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: $amount,
            currency: $currency
        ));
    }
    public function testHandleThrowsExceptionForZeroAmount()
    {
        $clientAccount = $this->createMock(Account::class);
        $merchantAccount = $this->createMock(Account::class);
        $currency = 'EUR';

        $clientAccount->balance = new Amount(2000, $currency);
        $merchantAccount->balance = new Amount(500, $currency);

        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', $clientAccount],
                ['merchant-account', $merchantAccount]
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Transaction amount must be more than 1.');

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: 0,
            currency: $currency
        ));
    }
    public function testHandleThrowsExceptionForNegativeAmount()
    {
        $clientAccount = $this->createMock(Account::class);
        $merchantAccount = $this->createMock(Account::class);
        $currency = 'EUR';

        $clientAccount->balance = new Amount(2000, $currency);
        $merchantAccount->balance = new Amount(500, $currency);

        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', $clientAccount],
                ['merchant-account', $merchantAccount]
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Transaction amount must be more than 1.');

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: -100,
            currency: $currency
        ));
    }
    public function testHandleThrowsExceptionForMerchantCurrencyMismatch()
    {
        $clientAccount = $this->createMock(Account::class);
        $merchantAccount = $this->createMock(Account::class);
        $amount = 1000;
        $transactionCurrency = 'USD';
        $merchantCurrency = 'EUR';

        $clientAccount->balance = new Amount(2000, $transactionCurrency);
        $merchantAccount->balance = new Amount(500, $merchantCurrency);

        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', $clientAccount],
                ['merchant-account', $merchantAccount]
            ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Merchant account currency does not match the transaction currency');

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: $amount,
            currency: $transactionCurrency
        ));
    }
    public function testHandleThrowsExceptionWhenMerchantAccountDoesNotExist(): void
    {
        $amount = 1000;
        $currency = 'EUR';
        $clientAccount = $this->createMock(Account::class);
        $clientAccount->balance = new Amount(2000, $currency);

        $this->accountRegistry->method('loadByNumber')
            ->willReturnMap([
                ['client-account', $clientAccount],
                ['merchant-account', null] // Simulate merchant account does not exist
            ]);

        $this->expectException(AccountDoesNotExistException::class);
        $this->expectExceptionMessage('merchant-account');

        $this->payByCardCommandHandler->handle(new PayByCardCommand(
            clientAccountNumber: 'client-account',
            merchantAccountNumber: 'merchant-account',
            amount: $amount,
            currency: $currency
        ));
    }
}
