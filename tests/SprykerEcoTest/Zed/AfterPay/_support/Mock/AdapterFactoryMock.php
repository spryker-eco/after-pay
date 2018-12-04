<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock;

use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactory;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\ApiVersionCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\AuthorizePaymentCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\AvailablePaymentMethodsCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\CaptureCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\LookupCustomerCallMock;

class AdapterFactoryMock extends AdapterFactory
{
    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
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
     * @return \SprykerEcoTest\Zed\AfterPay\Mock\Call\AuthorizePaymentCallMock
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
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCallInterface
     */
    public function createCaptureCall()
    {
        return new CaptureCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getAfterPayToMoneyBridge(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface
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
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface
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
