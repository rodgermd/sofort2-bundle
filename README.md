sofort2-bundle
==============

Symfony2 Bundle for Sofort PHP Library 2.x

Installation
------

### Add bundle
---

AppKernel.php:

```php
public function registerBundles()
{
    $bundles = array(
    ...
    new Sofort\SofortBundle(),
    ...
}
```

### Add controller routing
---

routing.yml:
``` yml
sofort_controller:
    resource: "@SofortBundle/Controller/"
    type: annotation
    prefix: /sofort
```

### Optionally add test config key

``` yml
sofort:
  test_key: 'XXX:XXX:XXXXXXXXXXXX'
```

Usage
-----

### Manager

``` php
  $manager = $container->get('sofort.manager')
  $manager->setConfigKey($sofortConfigKey);
```

### request create transaction

``` php
// Prepares model

$model = new PaymentRequestModel();
$model
  ->setAmount(0.1)
  ->setReason('test reason')
  ->setCountry('DE')
  ->setName('Max Mustermann')
  ->setAccountNumber('88888888')
  ->setBankCode('12345678');

// Call manager

$redirectResponse = $manager->createTransaction($model);
```

When calling $manager->createTransaction($model), the 'sofort.transaction.created' event is fired with TransactionCreateEvent argument.
The argument contains $response and $transactionId properties

### request transaction details

``` php
$response = $manager->requestTransaction($transactionId);
```

The $response is ant instance of SofortLibTransactionData
On successfull details retrieve, the event SofortEvents::DETAILS is fired with TransactionDetailsEvent as argument


SofortLibTransactionData methods available:

..getAmount
..getAmountRefunded
..getCount
..getPaymentMethod
..getConsumerProtection
..getStatus
..getStatusReason
..getStatusModifiedTime
..getLanguageCode
..getCurrency
..getTransaction
..getReason
..getUserVariable
..getTime
..getProjectId
..getRecipientHolder
..getRecipientAccountNumber
..getRecipientBankCode
..getRecipientCountryCode
..getRecipientBankName
..getRecipientBic
..getRecipientIban
..getSenderHolder
..getSenderAccountNumber
..getSenderHolder
..getSenderBankCode
..getSenderCountryCode
..getSenderBankName
..getSenderBic
..getSenderIban