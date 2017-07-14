<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter;

use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;

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
    public function sendAvailablePaymentMethodsRequest(
        AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer
    ) {
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
    public function sendAuthorizationRequest(
        AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
    ) {
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
    public function sendValidateCustomerRequest(
        AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
    ) {
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
    public function sendValidateBankAccountRequest(
        AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
    ) {
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
    public function sendLookupCustomerRequest(
        AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
    ) {
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
    public function sendLookupInstallmentPlansRequest(
        AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    ) {
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
    public function sendCaptureRequest(
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    ) {
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
    public function sendCancelRequest(
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        return $this
            ->adapterFactory
            ->createCancelCall()
            ->execute($cancelRequestTransfer);
    }

    /**
     * @return string
     */
    public function getApiVersion()
    {
        return $this
            ->adapterFactory
            ->createGetApiVersionCall()
            ->execute();
    }

    /**
     * @return string
     */
    public function getApiStatus()
    {
        return $this
            ->adapterFactory
            ->createGetApiStatusCall()
            ->execute();
    }
}
