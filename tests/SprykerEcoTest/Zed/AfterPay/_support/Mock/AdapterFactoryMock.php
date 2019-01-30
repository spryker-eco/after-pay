<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock;

use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactory;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\ApiStatusCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\ApiVersionCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\AuthorizePaymentCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\AvailablePaymentMethodsCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\CaptureCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\LookupCustomerCallMock;
use SprykerEcoTest\Zed\AfterPay\Mock\Call\LookupInstallmentPlansCallMock;

class AdapterFactoryMock extends AdapterFactory
{
    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
     */
    public function createAvailablePaymentMethodsCall(): AvailablePaymentMethodsCallInterface
    {
        return new AvailablePaymentMethodsCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface
     */
    public function createAuthorizePaymentCall(): AuthorizePaymentCallInterface
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
    public function createCaptureCall(): CaptureCallInterface
    {
        return new CaptureCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface
     */
    public function createApiVersionCall(): ApiVersionCallInterface
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
    public function createLookupCustomerCall(): LookupCustomerCallInterface
    {
        return new LookupCustomerCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface
     */
    public function createGetApiStatusCall(): ApiStatusCallInterface
    {
        return new ApiStatusCallMock(
            $this->createHttpClient(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface
     */
    public function createLookupInstallmentPlansCall(): LookupInstallmentPlansCallInterface
    {
        return new LookupInstallmentPlansCallMock(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig()
        );
    }
}
