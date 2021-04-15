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
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer $validateCustomerResponseTransfer */
        $validateCustomerResponseTransfer = $this->zedRequestClient->call('/after-pay/gateway/validate-customer-address', $validateCustomerRequestTransfer);

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
        $customerLookupResponseTransfer = $this->zedRequestClient->call('/after-pay/gateway/lookup-customer', $customerLookupRequestTransfer);

        return $customerLookupResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(
        AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    ): AfterPayInstallmentPlansResponseTransfer {
        /** @var \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer $installmentPlansResponseTransfer */
        $installmentPlansResponseTransfer = $this->zedRequestClient->call('/after-pay/gateway/lookup-installment-plans', $installmentPlansRequestTransfer);

        return $installmentPlansResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(
        AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
    ): AfterPayValidateBankAccountResponseTransfer {
        /** @var \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer $validateBankAccountResponseTransfer */
        $validateBankAccountResponseTransfer = $this->zedRequestClient->call('/after-pay/gateway/validate-bank-account', $bankAccountValidationRequestTransfer);

        return $validateBankAccountResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsTransfer
    {
        /** @var \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer $availablePaymentMethodsTransfer */
        $availablePaymentMethodsTransfer = $this->zedRequestClient
            ->call('/after-pay/gateway/get-available-payment-methods', $quoteTransfer);

        return $availablePaymentMethodsTransfer;
    }
}
