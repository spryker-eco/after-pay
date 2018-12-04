<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\Afterpay\AfterpayDependencyProvider;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiStatusCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiVersionCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AuthorizePaymentCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CancelCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CancelCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupCustomerCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\RefundCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\RefundCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateBankAccountCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateBankAccountCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateCustomerCall;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateCustomerCallInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\Http\Guzzle;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverter;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface;

/**
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 */
class AdapterFactory extends AbstractBusinessFactory implements AdapterFactoryInterface
{
    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
     */
    public function createAvailablePaymentMethodsCall(): AvailablePaymentMethodsCallInterface
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
    public function createAuthorizePaymentCall(): AuthorizePaymentCallInterface
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
    public function createValidateCustomerCall(): ValidateCustomerCallInterface
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
    public function createValidateBankAccountCall(): ValidateBankAccountCallInterface
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
    public function createLookupCustomerCall(): LookupCustomerCallInterface
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
    public function createLookupInstallmentPlansCall(): LookupInstallmentPlansCallInterface
    {
        return new LookupInstallmentPlansCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCallInterface
     */
    public function createCaptureCall(): CaptureCallInterface
    {
        return new CaptureCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CancelCallInterface
     */
    public function createCancelCall(): CancelCallInterface
    {
        return new CancelCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\RefundCallInterface
     */
    public function createRefundCall(): RefundCallInterface
    {
        return new RefundCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface
     */
    public function createGetApiVersionCall(): ApiVersionCallInterface
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
    public function createGetApiStatusCall(): ApiStatusCallInterface
    {
        return new ApiStatusCall(
            $this->createHttpClient(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface
     */
    public function createHttpClient(): ClientInterface
    {
        return new Guzzle(
            $this->getUtilEncodingService(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface
     */
    public function createTransferToCamelCaseArrayConverter(): TransferToCamelCaseArrayConverterInterface
    {
        return new TransferToCamelCaseArrayConverter($this->getUtilTextService());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface
     */
    public function getUtilEncodingService(): AfterpayToUtilEncodingInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    public function getMoneyFacade(): AfterpayToMoneyInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface
     */
    public function getUtilTextService(): AfterpayToUtilTextInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::SERVICE_UTIL_TEXT);
    }
}
