<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer;
use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCancelResponseTransfer;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayCaptureResponseTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRefundResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;

class AfterPayApiAdapter implements AdapterInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactoryInterface $adapterFactory
     */
    public function __construct(AdapterFactoryInterface $adapterFactory)
    {
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer
     */
    public function sendAvailablePaymentMethodsRequest(
        AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer
    ): AfterPayAvailablePaymentMethodsResponseTransfer {
        return $this->adapterFactory
            ->createAvailablePaymentMethodsCall()
            ->execute($requestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    public function sendAuthorizationRequest(AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer): AfterPayApiResponseTransfer
    {
        return $this->adapterFactory
            ->createAuthorizePaymentCall()
            ->execute($authorizeRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function sendValidateCustomerRequest(
        AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
    ): AfterPayValidateCustomerResponseTransfer {
        return $this->adapterFactory
            ->createValidateCustomerCall()
            ->execute($validateCustomerRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function sendValidateBankAccountRequest(
        AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
    ): AfterPayValidateBankAccountResponseTransfer {
        return $this->adapterFactory
            ->createValidateBankAccountCall()
            ->execute($validateBankAccountRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function sendLookupCustomerRequest(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer
    {
        return $this->adapterFactory
            ->createLookupCustomerCall()
            ->execute($customerLookupRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function sendLookupInstallmentPlansRequest(
        AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    ): AfterPayInstallmentPlansResponseTransfer {
        return $this->adapterFactory
            ->createLookupInstallmentPlansCall()
            ->execute($installmentPlansRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer
     */
    public function sendCaptureRequest(AfterPayCaptureRequestTransfer $captureRequestTransfer): AfterPayCaptureResponseTransfer
    {
        return $this->adapterFactory
            ->createCaptureCall()
            ->execute($captureRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelResponseTransfer
     */
    public function sendCancelRequest(AfterPayCancelRequestTransfer $cancelRequestTransfer): AfterPayCancelResponseTransfer
    {
        return $this->adapterFactory
            ->createCancelCall()
            ->execute($cancelRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundResponseTransfer
     */
    public function sendRefundRequest(AfterPayRefundRequestTransfer $refundRequestTransfer): AfterPayRefundResponseTransfer
    {
        return $this->adapterFactory
            ->createRefundCall()
            ->execute($refundRequestTransfer);
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->adapterFactory
            ->createApiVersionCall()
            ->execute();
    }

    /**
     * @return int
     */
    public function getApiStatus(): int
    {
        return $this->adapterFactory
            ->createGetApiStatusCall()
            ->execute();
    }
}
