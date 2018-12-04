<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter;

use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CancelCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\RefundCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateBankAccountCallInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateCustomerCallInterface;

interface AdapterFactoryInterface
{
    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCallInterface
     */
    public function createAvailablePaymentMethodsCall(): AvailablePaymentMethodsCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCallInterface
     */
    public function createAuthorizePaymentCall(): AuthorizePaymentCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateCustomerCallInterface
     */
    public function createValidateCustomerCall(): ValidateCustomerCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ValidateBankAccountCallInterface
     */
    public function createValidateBankAccountCall(): ValidateBankAccountCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCallInterface
     */
    public function createLookupCustomerCall(): LookupCustomerCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCallInterface
     */
    public function createCaptureCall(): CaptureCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CancelCallInterface
     */
    public function createCancelCall(): CancelCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\RefundCallInterface
     */
    public function createRefundCall(): RefundCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCallInterface
     */
    public function createGetApiVersionCall(): ApiVersionCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiStatusCallInterface
     */
    public function createGetApiStatusCall(): ApiStatusCallInterface;

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCallInterface
     */
    public function createLookupInstallmentPlansCall(): LookupInstallmentPlansCallInterface;
}
