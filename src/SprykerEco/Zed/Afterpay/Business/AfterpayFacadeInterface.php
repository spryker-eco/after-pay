<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterpayPaymentTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

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
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsTransfer;

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
    public function validateCustomerAddress(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterpayValidateCustomerResponseTransfer;

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
    public function validateBankAccount(AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterpayValidateBankAccountResponseTransfer;

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
    public function lookupCustomer(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterpayCustomerLookupResponseTransfer;

    /**
     * Specification:
     *  - Makes "lookup/installment-plans" call to the afterpay API, to get the available installment plans for the specific
     *  product/basket value. Returns monthly installment amount, interest and fees.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function lookupInstallmentPlans(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterpayInstallmentPlansResponseTransfer;

    /**
     * Specification:
     * - Sends payment authorize request to Afterpay gateway.
     * - Saves the transaction result in Quote for future recognition
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function authorizePayment(AfterpayCallTransfer $afterpayCallTransfer): AfterpayApiResponseTransfer;

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
     * @return void
     */
    public function capturePayment(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer): void;

    /**
     * Specification:
     * - Sends "refund" request to Afterpay gateway, to refund payment for a specific order item.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function refundPayment(ItemTransfer $itemTransfer, OrderTransfer $orderTransfer): void;

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
     * @return void
     */
    public function cancelPayment(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer): void;

    /**
     * Specification:
     * - Saves order payment method data according to quote and checkout response transfer data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void;

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
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer;

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder(int $idSalesOrder): AfterpayPaymentTransfer;

    /**
     * Specification:
     *  - Requests Afterpay API version and returns the result.
     *
     * @api
     *
     * @return string
     */
    public function getApiVersion(): string;

    /**
     * Specification:
     *  - Requests Afterpay API HTTP status and returns the result.
     *
     * @api
     *
     * @return int
     */
    public function getApiStatus(): int;
}
