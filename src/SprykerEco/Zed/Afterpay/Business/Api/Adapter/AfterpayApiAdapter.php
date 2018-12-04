<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelResponseTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureResponseTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundResponseTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer;

class AfterpayApiAdapter implements AdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactoryInterface
     */
    protected $adapterFactory;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactoryInterface $adapterFactory
     */
    public function __construct(AdapterFactoryInterface $adapterFactory)
    {
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer
     */
    public function sendAvailablePaymentMethodsRequest(AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer): AfterpayAvailablePaymentMethodsResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createAvailablePaymentMethodsCall()
            ->execute($requestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function sendAuthorizationRequest(AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer): AfterpayApiResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createAuthorizePaymentCall()
            ->execute($authorizeRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function sendValidateCustomerRequest(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterpayValidateCustomerResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createValidateCustomerCall()
            ->execute($validateCustomerRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function sendValidateBankAccountRequest(AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterpayValidateBankAccountResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createValidateBankAccountCall()
            ->execute($validateBankAccountRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function sendLookupCustomerRequest(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterpayCustomerLookupResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createLookupCustomerCall()
            ->execute($customerLookupRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function sendLookupInstallmentPlansRequest(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterpayInstallmentPlansResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createLookupInstallmentPlansCall()
            ->execute($installmentPlansRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    public function sendCaptureRequest(AfterpayCaptureRequestTransfer $captureRequestTransfer): AfterpayCaptureResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createCaptureCall()
            ->execute($captureRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    public function sendCancelRequest(AfterpayCancelRequestTransfer $cancelRequestTransfer): AfterpayCancelResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createCancelCall()
            ->execute($cancelRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    public function sendRefundRequest(AfterpayRefundRequestTransfer $refundRequestTransfer): AfterpayRefundResponseTransfer
    {
        return $this
            ->adapterFactory
            ->createRefundCall()
            ->execute($refundRequestTransfer);
    }

    /**
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this
            ->adapterFactory
            ->createGetApiVersionCall()
            ->execute();
    }

    /**
     * @return int
     */
    public function getApiStatus(): int
    {
        return $this
            ->adapterFactory
            ->createGetApiStatusCall()
            ->execute();
    }
}
