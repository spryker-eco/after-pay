<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\AfterPay\Zed;

use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Client\AfterPay\Dependency\Client\AfterPayToZedRequestClientInterface;

class AfterPayStub implements AfterPayStubInterface
{
    public const ZED_GET_AVAILABLE_PAYMENT_METHODS = '/after-pay/gateway/get-available-payment-methods';
    public const ZED_VALIDATE_CUSTOMER_ADDRESS = '/after-pay/gateway/validate-customer-address';
    public const ZED_VALIDATE_BANK_ACCOUNT = '/after-pay/gateway/validate-bank-account';
    public const ZED_LOOKUP_CUSTOMER = '/after-pay/gateway/lookup-customer';
    public const ZED_INSTALLMENT_PLANS = '/after-pay/gateway/lookup-installment-plans';

    /**
     * @var \SprykerEco\Client\AfterPay\Dependency\Client\AfterPayToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \SprykerEco\Client\AfterPay\Dependency\Client\AfterPayToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(AfterPayToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethodsByQuote(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer $availablePaymentMethodsTransfer */
        $availablePaymentMethodsTransfer = $this->zedRequestClient->call(static::ZED_GET_AVAILABLE_PAYMENT_METHODS, $quoteTransfer);

        return $availablePaymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer $validateCustomerResponseTransfer */
        $validateCustomerResponseTransfer = $this->zedRequestClient->call(static::ZED_VALIDATE_CUSTOMER_ADDRESS, $validateCustomerRequestTransfer);

        return $validateCustomerResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer $customerLookupResponseTransfer */
        $customerLookupResponseTransfer = $this->zedRequestClient->call(static::ZED_LOOKUP_CUSTOMER, $customerLookupRequestTransfer);

        return $customerLookupResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterPayInstallmentPlansResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer $installmentPlansResponseTransfer */
        $installmentPlansResponseTransfer = $this->zedRequestClient->call(static::ZED_INSTALLMENT_PLANS, $installmentPlansRequestTransfer);

        return $installmentPlansResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer): AfterPayValidateBankAccountResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer $validateBankAccountResponseTransfer */
        $validateBankAccountResponseTransfer = $this->zedRequestClient->call(static::ZED_VALIDATE_BANK_ACCOUNT, $bankAccountValidationRequestTransfer);

        return $validateBankAccountResponseTransfer;
    }
}
