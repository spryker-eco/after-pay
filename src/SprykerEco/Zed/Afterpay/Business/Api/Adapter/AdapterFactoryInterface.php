<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter;

interface AdapterFactoryInterface
{

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
     */
    public function createAvailablePaymentMethodsCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface
     */
    public function createAuthorizePaymentCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateCustomerCallInterface
     */
    public function createValidateCustomerCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ValidateBankAccountCallInterface
     */
    public function createValidateBankAccountCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface
     */
    public function createLookupCustomerCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCallInterface
     */
    public function createCaptureCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CancelCallInterface
     */
    public function createCancelCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface
     */
    public function createGetApiVersionCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface
     */
    public function createGetApiStatusCall();

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface
     */
    public function createLookupInstallmentPlansCall();

}
