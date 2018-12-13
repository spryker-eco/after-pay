<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterPayPaymentTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;

interface AfterPayFacadeInterface
{
    /**
     * Specification:
     * - Filters available payment methods depends on available payment methods from AfterPay.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer;

    /**
     * Specification:
     * - Makes a call to the "validate-address" API endpoint, to validate customer address.
     * Response contains isValid flag along with correctedAddress.
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
     *  - Makes "validate bank-account" call to the AfterPay API, to validate and evaluates the account and bank details
     *  in the context of direct debit payment. It is possible to transfer either the combination of BankCode and AccountNumber or IBAN and BIC
     *  Response contains validation result and list of risk-check messages
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterPayValidateBankAccountResponseTransfer;

    /**
     * Specification:
     *  - Makes "customer-lookup" call to the AfterPay API, to find customer based on social security number or mobile number.
     *  Response contains customer's account with list of addresses
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
     *  - Makes "lookup/installment-plans" call to the AfterPay API, to get the available installment plans for the specific
     *  product/basket value. Returns monthly installment amount, interest and fees.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function lookupInstallmentPlans(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterPayInstallmentPlansResponseTransfer;

    /**
     * Specification:
     * - Sends payment authorize request to AfterPay gateway.
     * - Saves the transaction result in Quote for future recognition
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    public function authorizePayment(AfterPayCallTransfer $afterPayCallTransfer): AfterPayApiResponseTransfer;

    /**
     * Specification:
     * - Sends payment capture request to AfterPay gateway, to capture payment for a specific order item.
     * - If it is the first item capture request for given order, captures also full expense amount.
     * - Saves the transaction result in DB and updates payment with new total captured amount.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    public function capturePayment(array $items, AfterPayCallTransfer $afterPayCallTransfer): void;

    /**
     * Specification:
     * - Sends "refund" request to AfterPay gateway, to refund payment for a specific order item.
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
     * - Sends "void" request to AfterPay gateway, to cancel payment for a specific order item, before payment is captured
     * - If it is the last item cancellation request for given order, cancels also full expense amount.
     * - Saves the transaction result in DB and updates payment with new total cancelled amount.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    public function cancelPayment(ItemTransfer $itemTransfer, AfterPayCallTransfer $afterPayCallTransfer): void;

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
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder(int $idSalesOrder): AfterPayPaymentTransfer;

    /**
     * Specification:
     *  - Requests AfterPay API version and returns the result.
     *
     * @api
     *
     * @return string
     */
    public function getApiVersion(): string;

    /**
     * Specification:
     *  - Requests AfterPay API HTTP status and returns the result.
     *
     * @api
     *
     * @return int
     */
    public function getApiStatus(): int;
}
