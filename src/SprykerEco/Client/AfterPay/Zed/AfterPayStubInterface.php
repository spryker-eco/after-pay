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

interface AfterPayStubInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(
        AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    ): AfterPayInstallmentPlansResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(
        AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
    ): AfterPayValidateBankAccountResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsTransfer;
}
