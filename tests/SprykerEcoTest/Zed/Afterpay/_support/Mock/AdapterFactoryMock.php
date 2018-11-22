<?php

namespace SprykerEcoTest\Zed\Afterpay\Mock;

use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactory;
use SprykerEcoTest\Zed\Afterpay\Mock\Call\ApiVersionCallMock;
use SprykerEcoTest\Zed\Afterpay\Mock\Call\AuthorizePaymentCallMock;
use SprykerEcoTest\Zed\Afterpay\Mock\Call\AvailablePaymentMethodsCallMock;
use SprykerEcoTest\Zed\Afterpay\Mock\Call\CaptureCallMock;
use SprykerEcoTest\Zed\Afterpay\Mock\Call\LookupCustomerCallMock;

class AdapterFactoryMock extends AdapterFactory
{
    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
     */
    public function createAvailablePaymentMethodsCall()
    {
        return new AvailablePaymentMethodsCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEcoTest\Zed\Afterpay\Mock\Call\AuthorizePaymentCallMock
     */
    public function createAuthorizePaymentCall()
    {
        return new AuthorizePaymentCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCallInterface
     */
    public function createCaptureCall()
    {
        return new CaptureCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getAfterpayToMoneyBridge(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface
     */
    public function createGetApiVersionCallMock()
    {
        return new ApiVersionCallMock(
            $this->createHttpClient(),
            $this->getConfig(),
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface
     */
    public function createLookupCustomerCall()
    {
        return new LookupCustomerCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }
}
