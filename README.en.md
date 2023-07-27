# Skaleet Interview

# Usage

## Prerequisites

- Install PHPStorm
- Install Docker and/or PHP 8.0 with Composer

## Installation

- Clone the project: `git clone git@gitlab.com:skaleet-public/interview/interview.git`

- Navigate to the project folder: `cd interview`

- Install dependencies either using composer: `composer install` or using Docker: `docker-compose run install`

## Running Tests

Run PHPUnit tests:

- `./vendor/bin/phpunit`

or

Run tests using Docker:

- `docker-compose run test`

## Running the Use Case

Run the use case to process a payment:

`php bin/console.php pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

or

Run the use case using Docker:

`docker-compose run console pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

## Managing the Database
The database is a file containing serialized PHP objects. The file is named "db" and located at the root of the project. Two commands are available to interact with it:

View the database contents:

`php bin/console.php database:dump` or `docker-compose run console database:dump`

Clear the database to its initial state:

`php bin/console.php database:clear` or `docker-compose run console database:clear`

## Specification: Transaction Processing

### Existing Environment

#### Available Classes:

The described behavior must be implemented in the method `PayByCardCommandHandler::handle()`

The project exposes a CLI command (PayByCardCli) to launch the use case.
The project provides a PayByCardCommandHandlerTest class and a phpunit.xml file to run the project's unit tests (and calculate code coverage).

#### Constraints

- The `PayByCardCommand` class must not be modified.
- The name and parameters provided to the method `PayByCardCommandHandler::handle()` must not be modified.
- The behavior and signature of existing methods in the `InMemoryDatabase` class must not be modified. New methods can be added if necessary.
- Apart from the specified classes above, any other class can be modified/added/deleted.

### Exercise 1: Pay by Card

#### Use Case Description
A client visits a merchant and wishes to make a payment using a credit card.
They place the card on the payment terminal, and a request is sent to the system to validate the transaction.

- The input amount is strictly positive.
- The currency of the affected accounts and the payment must be the same.
- The client's account is debited with the transaction amount.
- The merchant's account is credited with the transaction amount.
- The date of the transaction is the current date at the time of payment.

Note: The amounts are modeled in cents. Therefore, 100 represents 1.00 €.

##### Acceptance Criteria
- The balances of the accounts are updated based on the transaction parameters.
- The transaction is recorded in the history, along with the movements made on the accounts.

##### Example 1

- The client has a balance of 150€
- The merchant has a balance of 2,500€
- The bank has a balance of 10,000€

- The client makes a payment of 15.36 €


|                 | Client's Account | Merchant's Account |
|-----------------|------------------|----------------------|
| *Initial Balance* | 150 €            | 2 500 €              |
| *Payment*      | -15.36 €         | +15.36 €             |
| *Final Balance*   | 134.64 €         | 2 515.36 €           |



Exercise 2: Pay by Card: Going Further

Use Case Description
In addition to the use case developed in Exercise 1, add the following business rules:

A fee of 2% of the transaction amount is applied during the operation. The merchant's account is debited with the
amount of these fees, and the bank's account is credited.
The fees are capped at a maximum of 3€.
The client's balance cannot go below 0€.
The merchant's balance cannot exceed 3,000 €.
The merchant's balance cannot go below -1,000 €.

Example 2

The client has a balance of 150€
The merchant has a balance of 2,500€
The bank has a balance of 10,000€

The client makes a payment of 15.36 €

Bank's Account
Client's Account
Merchant's Account

Initial Balance
10,000 €
150 €
2,500 €

Payment

-15.36 €
+15.36 €

Fees
+0.31 €

-0.31 €

Final Balance
10,000.31 €
134.64 €
2,515.05 €
