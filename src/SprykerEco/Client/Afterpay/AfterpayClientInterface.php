<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Afterpay;

use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface AfterpayClientInterface
{

    /**
     * Specification:
     *  - Makes "payment-methods" call to the afterpay API, in order to get list of available
     *  payment methods for the given quote, with risk check score.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     *  - Makes "validate-address" call to the afterpay API, in order to validate customer address.
     *  Response contains isValid flag along with correctedAddress.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer);

    /**
     * Specification:
     *  - Makes "customer-lookup" call to the afterpay API, to find customer based on social security number or mobile number.
     *  Response contains customer's account with list of addresses
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer);

    /**
     * Specification:
     *  - Makes "lookup/installment-plans" call to the afterpay API, to get the available installment plans for the specific
     *  product/basket value. Returns monthly installment amount, interest and fees.
     *
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer);

    /**
     * Specification:
     *  - Makes "validate bank-account" call to the afterpay API, to validate and evaluates the account and bank details
     *  in the context of direct debit payment. It is possible to transfer either the combination of BankCode and AccountNumber or IBAN and BIC
     *  Response contains validation result and list of risk-check messages
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterpayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer);

    /**
     * Specification:
     *  - Retrieve quote from current customer session
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getQuoteFromSession();

}
