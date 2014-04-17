<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 11:45
 */

namespace Sofort\Test;

use Sofort\Model\PaymentRequestModel;
use Sofort\Status\Status;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator;

/**
 * Class PaymentTest
 *
 * @package Sofort\Test
 */
class PaymentTest extends WebTestCase
{
    /** @var Client */
    protected $client;
    /** @var Container */
    protected $container;
    /** @var  Validator */
    protected $validator;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->client    = static::createClient();
        $this->container = $this->client->getContainer();
        $this->validator = $this->container->get('validator');
    }

    /**
     * Tests model validation
     */
    public function testModel()
    {
        $model = new PaymentRequestModel();

        $errors = $this->validator->validate($model);

        $this->assertTrue($this->hasError('amount', $errors));
        $this->assertTrue($this->hasError('accountNumber', $errors));
        $this->assertTrue($this->hasError('bankCode', $errors));
        $this->assertTrue($this->hasError('name', $errors));
        $this->assertTrue($this->hasError('country', $errors));
        $this->assertTrue($this->hasError('reason', $errors));

        // test amount
        $model->setAmount(-2);
        $errors = $this->validator->validate($model);
        $this->assertTrue($this->hasError('amount', $errors));
        $model->setAmount(10);
        $errors = $this->validator->validate($model);
        $this->assertFalse($this->hasError('amount', $errors));

        // test account number
        $model->setAccountNumber('88888888');
        $errors = $this->validator->validate($model);
        $this->assertFalse($this->hasError('accountNumber', $errors));

        // test bank code
        $model->setBankCode('12345678');
        $errors = $this->validator->validate($model);
        $this->assertFalse($this->hasError('bankCode', $errors));

        // test name
        $model->setName('Max Mustermann');
        $errors = $this->validator->validate($model);
        $this->assertFalse($this->hasError('name', $errors));

        // test country
        $model->setCountry('AAAA');
        $errors = $this->validator->validate($model);
        $this->assertTrue($this->hasError('country', $errors));
        $model->setCountry('DE');
        $errors = $this->validator->validate($model);
        $this->assertFalse($this->hasError('country', $errors));

        // test reason
        $model->setReason('test reason');
        $errors = $this->validator->validate($model);
        $this->assertFalse($this->hasError('reason', $errors));

        // test email
        $this->assertFalse($this->hasError('email', $errors));
        $model->setEmail('test email');
        $errors = $this->validator->validate($model);
        $this->assertTrue($this->hasError('email', $errors));
        $model->setEmail('test@example.com');
        $errors = $this->validator->validate($model);
        $this->assertFalse($this->hasError('email', $errors));
    }

    /**
     * Tests payment
     */
    public function testPayment()
    {
        $this->container->get('router')->getContext()->setHost('www.google.com');
        $manager = $this->container->get('sofort.manager');
        $model   = new PaymentRequestModel();
        $model
            ->setAmount(0.1)
            ->setReason('test reason')
            ->setCountry('DE')
            ->setName('Max Mustermann')
            ->setAccountNumber('88888888')
            ->setBankCode('12345678');

        $response = $manager->createTransaction($model);

        $this->assertTrue($response instanceof RedirectResponse);
    }

    /**
     * Tests request transaction
     */
    public function testTransactionDetails()
    {
        $manager       = $this->container->get('sofort.manager');
        $transactionId = '84116-181122-534E849F-919D'; // test transaction id, can be failed?
        $response      = $manager->requestTransaction($transactionId);

        $this->assertEquals($transactionId, $response->getTransaction());
        $this->assertEquals(1, $response->getAmount());
        $this->assertEquals(Status::PENDING, $response->getStatus()); // Should be pending because of test shop
        $this->assertEquals('EUR', $response->getCurrency());
        $this->assertEquals('23456789', $response->getSenderAccountNumber());
        $this->assertEquals('00000', $response->getSenderBankCode());
        $this->assertEquals('DE06000000000023456789', $response->getSenderIban());
        $this->assertEquals('SFRTDE20XXX', $response->getSenderBic());
        $this->assertEquals('Max Mustermann', $response->getSenderHolder());
    }

    /**
     * Checks if contains an error
     *
     * @param string                  $name
     * @param ConstraintViolationList $errors
     *
     * @return bool
     */
    protected function hasError($name, ConstraintViolationList $errors)
    {
        foreach ($errors as $error) {
            /** @var ConstraintViolation $error */
            if ($error->getPropertyPath() == $name) {
                return true;
            }
        }

        return false;
    }
}
