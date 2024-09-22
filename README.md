# Skaleet Interview

# Usage

## Prerequisites

- Install PHPStorm.
- Install Docker and/or PHP 8.0 with Composer.

## Installation

- Clone the project: `git clone git@gitlab.com:skaleet-public/interview/interview.git`
- Navigate to the directory: `cd interview`
- Install dependencies: `composer install` or `docker-compose run install`

# Evaluation Criteria
For this exercise, your priority is to develop code that is readable, tested, and maintainable.
We will assess your knowledge of SOLID principles, your skills in automated testing, hexagonal architecture, and tactical patterns of Domain Driven Design.

It is not necessary to have implemented all business rules to pass this test.
We prefer a candidate who does not implement all the rules but delivers code they are proud of.

## Exercise #1: Pay by Card

### Use Case Description

A customer visits a merchant and wishes to make a payment using a credit card. They place the card on the payment terminal, and a request is sent to the system to validate the transaction.

You must implement the business logic when such a request is processed by the system.
Here is the list of business rules to implement:
- The input amount must be strictly positive.
- The currency of the impacted accounts and the payment must be the same.
- The customer's account is debited by the transaction amount.
- The merchant's account is credited with the transaction amount.
- The transaction date is the current date at the time of payment.

**Note:** Amounts are modeled in cents. So, `100` represents `1.00 €`.

### Acceptance Criteria

The account balances are updated based on the transaction parameters. The transaction is recorded along with the movements made on the accounts.

### Example 1

- A customer has a balance of €150.
- A merchant has a balance of €2,500.
- The bank has a balance of €10,000.

The customer makes a payment of €15.36.

|                 | Customer's Account | Merchant's Account |
|-----------------|--------------------|--------------------|
| *initial balance* | €150              | €2,500             |
| *payment*        | -€15.36           | +€15.36            |
| *final balance*  | €134.64           | €2,515.36          |


## Existing Environment

### Available Classes
- The described behavior should be implemented in the `PayByCardCommandHandler::handle()` method.
- The project exposes a CLI command (`PayByCardCli`) to execute the use case.
- The project provides a class `PayByCardCommandHandlerTest` and a `phpunit.xml` file for running unit tests for the project (and calculating code coverage).

### Constraints
- The `PayByCardCommand` class should not be modified.
- The name and parameters provided to the `PayByCardCommandHandler::handle()` method should not be modified.
- The behavior and signature of existing methods in the `InMemoryDatabase` class should not be modified. It is possible to add new methods if necessary.
- Besides the specified classes above, any other class can be modified/added/deleted.

<<<<<<< HEAD
- clonez le projet : `git clone git@gitlab.com:skaleet-public/interview/interview.git`
- positionnez vous dans le dossier : `cd interview`
- installez les dépendances `composer install` ou `docker-compose run install`

# Critères d'évaluation

Pour cet exercice votre priorité est de développer un code lisible, testé et maintenable.
Nous évaluerons vos connaissances des principes SOLID, vos compétences en tests automatisés, architecture hexagonale et les tactical patterns du Domain Driven Design.

Il n'est pas nécessaire d'avoir implémenté toutes les règles de gestion pour réussir ce test.
Nous préférons un candidat qui n'implémente pas toutes les règles, mais qui livre un code dont il est fier.



# Exercice #1 :  Pay by card

## Description du use case

Un client se rend chez un commerçant et souhaite régler ses achats par carte bancaire.
Il positionne la carte sur le terminal de paiement et une requête est envoyée au système pour valider la transaction.

Vous devez implémenter la logique métier qui se déclenche lorsqu'un tel appel arrive sur le système.
Voici la liste des règles de gestions à implémenter :
- Le montant fourni en entrée est strictement positif.
- La devise des comptes impactés et du paiement doivent être identiques.
- Le compte du client est débité du montant de la transaction.
- Le compte du commerçant est crédité du montant de la transaction.
- La date de la transaction est la date courante au moment du paiement.

**Attention** : les montants sont modélisés en centimes. Donc `100` vaut `1.00 €`.

## Critères d'acceptance

Le solde des comptes est mis à jour en fonction des paramètres de la transaction.
La transaction est historisée ainsi que les mouvements réalisés sur les comptes.

## Exemple 1

- Un client a un solde de 150€
- Un commerçant a un solde de 2 500€
- La banque a un solde 10 000€

Le client fait un paiement de 15.36 €

|                 | Compte du client | Compte du commerçant |
|-----------------|------------------|----------------------|
| *solde initial* | 150 €            | 2 500 €              |
| *paiement*      | -15.36 €         | +15.36 €             |
| *solde final*   | 134.64 €         | 2 515.36 €           |


# Environnement existant

## Classes à disposition
- Le comportement décrit doit être implémenté dans la méthode `PayByCardCommandHandler::handle()`
- Le projet expose une commande CLI  (`PayByCardCli`) permettant de lancer le use case.
- Le projet expose une classe `PayByCardCommandHandlerTest` et un fichier `phpunit.xml` permettant de lancer les tests unitaires du projet (et calculer le code coverage)


## Contraintes
- La classe `PayByCardCommand` ne doit pas être modifiée
- Le nom et les paramètres fournis à la méthode `PayByCardCommandHandler::handle()` ne doivent pas être modifiés
- Le comportement et la signature des méthodes existantes de la classe `InMemoryDatabase` ne doivent pas être modifiés. Il est possible d'y ajouter de nouvelles méthodes si besoin
- Hormis les classes spécifiées ci-dessus, n'importe quelle autre classe peut être modifiée/ajoutée/supprimée


## Lancer les tests
=======
### Run Tests
>>>>>>> debe835 (Implement exercise 1)

- `./vendor/bin/phpunit`

or

- `docker-compose run test`

### Execute the Use Case

- `php bin/console.php pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

or

- `docker-compose run console pay {clientAccountNumber} {amount} {currency} {merchantAccountNumber}`

### Manage the Database
This is a database in the form of a file containing serialized PHP objects. The file is named `db` at the root of the project.

Two commands can interact with it:
- `php bin/console.php database:dump` or `docker-compose run console database:dump`: to view its content.
- `php bin/console.php database:clear` or `docker-compose run console database:clear`: to reset it to its initial state.

## Exercise #2: Going Further - Fee Management
This exercise is not to be completed during the technical test. It is for internal training purposes.

### Use Case Description

In addition to the use case developed in exercise 1, add the following business rules:

- A fee of 2% of the transaction amount is applied during the operation. The merchant's account is debited, and the bank's account is credited with the fee amount.
- Fees are capped at a maximum of €3.
- The customer's balance cannot be less than €0.
- The merchant's balance cannot exceed €3,000.
- The merchant's balance cannot be less than -€1,000.

### Example 2

- A customer has a balance of €150.
- A merchant has a balance of €2,500.
- The bank has a balance of €10,000.

The customer makes a payment of €15.36.

|                 | Bank's Account | Customer's Account | Merchant's Account |
|-----------------|----------------|--------------------|--------------------|
| *initial balance* | €10,000        | €150               | €2,500             |
| *payment*        |                | -€15.36            | +€15.36            |
| *fees*           | +€0.31         |                    | -€0.31             |
| *final balance*  | €10,000.31     | €134.64            | €2,515.05          |
