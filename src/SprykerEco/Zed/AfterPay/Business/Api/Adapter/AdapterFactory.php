<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\AfterPay\AfterPayDependencyProvider;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiStatusCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CancelCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CancelCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\RefundCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\RefundCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateBankAccountCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateBankAccountCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateCustomerCall;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateCustomerCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\Http\Guzzle;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverter;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilTextServiceInterface;

/**
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 */
class AdapterFactory extends AbstractBusinessFactory implements AdapterFactoryInterface
{
    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
     */
    public function createAvailablePaymentMethodsCall(): AvailablePaymentMethodsCallInterface
    {
        return new AvailablePaymentMethodsCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface
     */
    public function createAuthorizePaymentCall(): AuthorizePaymentCallInterface
    {
        return new AuthorizePaymentCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateCustomerCallInterface
     */
    public function createValidateCustomerCall(): ValidateCustomerCallInterface
    {
        return new ValidateCustomerCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getUtilTextService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateBankAccountCallInterface
     */
    public function createValidateBankAccountCall(): ValidateBankAccountCallInterface
    {
        return new ValidateBankAccountCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface
     */
    public function createLookupCustomerCall(): LookupCustomerCallInterface
    {
        return new LookupCustomerCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface
     */
    public function createLookupInstallmentPlansCall(): LookupInstallmentPlansCallInterface
    {
        return new LookupInstallmentPlansCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCallInterface
     */
    public function createCaptureCall(): CaptureCallInterface
    {
        return new CaptureCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CancelCallInterface
     */
    public function createCancelCall(): CancelCallInterface
    {
        return new CancelCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\RefundCallInterface
     */
    public function createRefundCall(): RefundCallInterface
    {
        return new RefundCall(
            $this->createHttpClient(),
            $this->createTransferToCamelCaseArrayConverter(),
            $this->getUtilEncodingService(),
            $this->getMoneyFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface
     */
    public function createApiVersionCall(): ApiVersionCallInterface
    {
        return new ApiVersionCall(
            $this->createHttpClient(),
            $this->getConfig(),
            $this->getUtilEncodingService(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface
     */
    public function createGetApiStatusCall(): ApiStatusCallInterface
    {
        return new ApiStatusCall(
            $this->createHttpClient(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface
     */
    public function createHttpClient(): ClientInterface
    {
        return new Guzzle(
            $this->getUtilEncodingService(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface
     */
    public function createTransferToCamelCaseArrayConverter(): TransferToCamelCaseArrayConverterInterface
    {
        return new TransferToCamelCaseArrayConverter($this->getUtilTextService());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): AfterPayToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    public function getMoneyFacade(): AfterPayToMoneyFacadeInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilTextServiceInterface
     */
    public function getUtilTextService(): AfterPayToUtilTextServiceInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::SERVICE_UTIL_TEXT);
    }
}
