<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\Afterpay\AfterpayDependencyProvider;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiStatusCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiVersionCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AuthorizePaymentCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CancelCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupCustomerCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateBankAccountCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateCustomerCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\Http\Guzzle;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverter;

/**
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 */
class AdapterFactory extends AbstractBusinessFactory implements AdapterFactoryInterface
{

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
     */
    public function createAvailablePaymentMethodsCall()
    {
        return new AvailablePaymentMethodsCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface
     */
    public function createAuthorizePaymentCall()
    {
        return new AuthorizePaymentCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateCustomerCallInterface
     */
    public function createValidateCustomerCall()
    {
        return new ValidateCustomerCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getUtilTextService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateBankAccountCallInterface
     */
    public function createValidateBankAccountCall()
    {
        return new ValidateBankAccountCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface
     */
    public function createLookupCustomerCall()
    {
        return new LookupCustomerCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface
     */
    public function createLookupInstallmentPlansCall()
    {
        return new LookupInstallmentPlansCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getAfterpayToMoneyBridge(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCallInterface
     */
    public function createCaptureCall()
    {
        return new CaptureCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getAfterpayToMoneyBridge(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CancelCallInterface
     */
    public function createCancelCall()
    {
        return new CancelCall(
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
    public function createGetApiVersionCall()
    {
        return new ApiVersionCall(
            $this->createHttpClient(),
            $this->getConfig(),
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface
     */
    public function createGetApiStatusCall()
    {
        return new ApiStatusCall(
            $this->createHttpClient(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface
     */
    protected function createHttpClient()
    {
        return new Guzzle($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface
     */
    protected function createTransferToCamelCaseArrayConverter()
    {
        return new TransferToCamelCaseArrayConverter(
            $this->getUtilTextService()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface
     */
    protected function getUtilEncodingService()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyBridge
     */
    protected function getAfterpayToMoneyBridge()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface
     */
    protected function getUtilTextService()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::SERVICE_UTIL_TEXT);
    }

}
