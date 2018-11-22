<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Afterpay\Zed;

use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\ZedRequest\Stub\ZedRequestStub;

class AfterpayStub extends ZedRequestStub implements AfterpayStubInterface
{
    const ZED_GET_AVAILABLE_PAYMENT_METHODS = '/afterpay/gateway/get-available-payment-methods';
    const ZED_VALIDATE_CUSTOMER_ADDRESS = '/afterpay/gateway/validate-customer-address';
    const ZED_VALIDATE_BANK_ACCOUNT = '/afterpay/gateway/validate-bank-account';
    const ZED_LOOKUP_CUSTOMER = '/afterpay/gateway/lookup-customer';
    const ZED_INSTALLMENT_PLANS = '/afterpay/gateway/lookup-installment-plans';

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface|\Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethodsByQuote(QuoteTransfer $quoteTransfer)
    {
        return $this->zedStub->call(
            static::ZED_GET_AVAILABLE_PAYMENT_METHODS,
            $quoteTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface|\Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer)
    {
        return $this->zedStub->call(
            static::ZED_VALIDATE_CUSTOMER_ADDRESS,
            $validateCustomerRequestTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface|\Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer)
    {
        return $this->zedStub->call(
            static::ZED_LOOKUP_CUSTOMER,
            $customerLookupRequestTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface|\Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer)
    {
        return $this->zedStub->call(
            static::ZED_INSTALLMENT_PLANS,
            $installmentPlansRequestTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Spryker\Shared\Kernel\Transfer\TransferInterface|\Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterpayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer)
    {
        return $this->zedStub->call(
            static::ZED_VALIDATE_BANK_ACCOUNT,
            $bankAccountValidationRequestTransfer
        );
    }
}
