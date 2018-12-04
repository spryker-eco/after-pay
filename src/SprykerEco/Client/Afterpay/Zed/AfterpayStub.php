<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Afterpay\Zed;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Client\Afterpay\Dependency\Client\AfterpayToZedRequestClientInterface;

class AfterpayStub implements AfterpayStubInterface
{
    public const ZED_GET_AVAILABLE_PAYMENT_METHODS = '/afterpay/gateway/get-available-payment-methods';
    public const ZED_VALIDATE_CUSTOMER_ADDRESS = '/afterpay/gateway/validate-customer-address';
    public const ZED_VALIDATE_BANK_ACCOUNT = '/afterpay/gateway/validate-bank-account';
    public const ZED_LOOKUP_CUSTOMER = '/afterpay/gateway/lookup-customer';
    public const ZED_INSTALLMENT_PLANS = '/afterpay/gateway/lookup-installment-plans';

    /**
     * @var \SprykerEco\Client\Afterpay\Dependency\Client\AfterpayToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \SprykerEco\Client\Afterpay\Dependency\Client\AfterpayToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(AfterpayToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethodsByQuote(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer $availablePaymentMethodsTransfer */
        $availablePaymentMethodsTransfer = $this->zedRequestClient->call(static::ZED_GET_AVAILABLE_PAYMENT_METHODS, $quoteTransfer);

        return $availablePaymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterpayValidateCustomerResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer $validateCustomerResponseTransfer */
        $validateCustomerResponseTransfer = $this->zedRequestClient->call(static::ZED_VALIDATE_CUSTOMER_ADDRESS, $validateCustomerRequestTransfer);

        return $validateCustomerResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterpayCustomerLookupResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer $customerLookupResponseTransfer */
        $customerLookupResponseTransfer = $this->zedRequestClient->call(static::ZED_LOOKUP_CUSTOMER, $customerLookupRequestTransfer);

        return $customerLookupResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterpayInstallmentPlansResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer $installmentPlansResponseTransfer */
        $installmentPlansResponseTransfer = $this->zedRequestClient->call(static::ZED_INSTALLMENT_PLANS, $installmentPlansRequestTransfer);

        return $installmentPlansResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterpayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer): AfterpayValidateBankAccountResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer $validateBankAccountResponseTransfer */
        $validateBankAccountResponseTransfer = $this->zedRequestClient->call(static::ZED_VALIDATE_BANK_ACCOUNT, $bankAccountValidationRequestTransfer);

        return $validateBankAccountResponseTransfer;
    }
}
