<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface AfterpayFacadeInterface
{
    /**
     * Specification:
     * - Makes a call to the "payment-methods" API endpoint, to get a list of payment methods,
     * available for the current quote, with additional information - checkout_id, and risk_check_score
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
     * - Makes a call to the "validate-address" API endpoint, to validate customer address.
     * Response contains isValid flag along with correctedAddress.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(
        AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
    );

    /**
     * Specification:
     *  - Makes "validate bank-account" call to the afterpay API, to validate and evaluates the account and bank details
     *  in the context of direct debit payment. It is possible to transfer either the combination of BankCode and AccountNumber or IBAN and BIC
     *  Response contains validation result and list of risk-check messages
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(
        AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
    );

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
    public function lookupCustomer(
        AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
    );

    /**
     * Specification:
     *  - Makes "lookup/installment-plans" call to the afterpay API, to get the available installment plans for the specific
     *  product/basket value. Returns monthly installment amount, interest and fees.
     *
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function lookupInstallmentPlans(
        AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    );

    /**
     * Specification:
     * - Sends payment authorize request to Afterpay gateway.
     * - Saves the transaction result in Quote for future recognition
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCallTransfer
     */
    public function authorizePayment(AfterpayCallTransfer $afterpayCallTransfer);

    /**
     * Specification:
     * - Sends payment capture request to Afterpay gateway, to capture payment for a specific order item.
     * - If it is the first item capture request for given order, captures also full expense amount.
     * - Saves the transaction result in DB and updates payment with new total captured amount.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayResponseTransfer
     */
    public function capturePayment(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer);

    /**
     * Specification:
     * - Sends "refund" request to Afterpay gateway, to refund payment for a specific order item.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayResponseTransfer
     */
    public function refundPayment(ItemTransfer $itemTransfer, OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Sends "void" request to Afterpay gateway, to cancel payment for a specific order item, before payment is captured
     * - If it is the last item cancellation request for given order, cancels also full expense amount.
     * - Saves the transaction result in DB and updates payment with new total cancelled amount.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayResponseTransfer
     */
    public function cancelPayment(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer);

    /**
     * Specification:
     * - Saves order payment method data according to quote and checkout response transfer data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer);

    /**
     * Specification:
     *  - Executes a post save hook for the following payment methods:
     *    Sofort / authorize: checks for an external redirect URL in transaction log and redirects customer to the payment system
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer);

    /**
     * Specification:
     *  - Requests Afterpay API version and returns the result.
     *
     * @api
     *
     * @return string
     */
    public function getApiVersion();

    /**
     * Specification:
     *  - Requests Afterpay API HTTP status and returns the result.
     *
     * @api
     *
     * @return int
     */
    public function getApiStatus();
}
