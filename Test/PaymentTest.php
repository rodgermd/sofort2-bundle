<?php
/**
 * Created by PhpStorm.
 * User: rodger
 * Date: 16.04.14
 * Time: 11:45
 */

namespace Sofort\Test;


use Sofort\Model\PaymentRequestModel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\Container;
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
        $manager = $this->container->get('sofort.manager');
        $model = new PaymentRequestModel();
        $model
            ->setAmount(10)
            ->setReason('test reason')
            ->setCountry('DE')
            ->setName('Max Mustermann')
            ->setAccountNumber('88888888')
            ->setBankCode('12345678');

        $manager->pay($model);
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