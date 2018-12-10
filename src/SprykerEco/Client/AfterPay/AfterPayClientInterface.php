<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\AfterPay;

use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;

interface AfterPayClientInterface
{
    /**
     * Specification:
     *  - Makes "validate-address" call to the AfterPay API, in order to validate customer address.
     *  - Response contains isValid flag along with correctedAddress.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer;

    /**
     * Specification:
     *  - Makes "customer-lookup" call to the AfterPay API, to find customer based on social security number or mobile number.
     *  - Response contains customer's account with list of addresses.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer;

    /**
     * Specification:
     *  - Makes "lookup/installment-plans" call to the AfterPay API, to get the available installment plans for the specific product/basket value.
     *  - Returns monthly installment amount, interest and fees.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterPayInstallmentPlansResponseTransfer;

    /**
     * Specification:
     *  - Makes "validate bank-account" call to the AfterPay API, to validate and evaluates the account and bank details in the context of direct debit payment.
     *  - It is possible to transfer either the combination of BankCode and AccountNumber or IBAN and BIC.
     *  - Response contains validation result and list of risk-check messages.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer): AfterPayValidateBankAccountResponseTransfer;
}
