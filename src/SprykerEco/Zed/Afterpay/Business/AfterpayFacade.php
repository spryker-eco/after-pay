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
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \SprykerEco\Zed\Afterpay\Business\AfterpayBusinessFactory getFactory()
 */
class AfterpayFacade extends AbstractFacade implements AfterpayFacadeInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsTransfer
    {
        return $this
            ->getFactory()
            ->createAvailablePaymentMethodsHandler()
            ->getAvailablePaymentMethods($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterpayValidateCustomerResponseTransfer
    {
        return $this
            ->getFactory()
            ->createValidateCustomerHandler()
            ->validateCustomer($validateCustomerRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterpayValidateBankAccountResponseTransfer
    {
        return $this
            ->getFactory()
            ->createValidateBankAccountHandler()
            ->validateBankAccount($validateBankAccountRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterpayCustomerLookupResponseTransfer
    {
        return $this
            ->getFactory()
            ->createLookupCustomerHandler()
            ->lookupCustomer($customerLookupRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function lookupInstallmentPlans(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterpayInstallmentPlansResponseTransfer
    {
        return $this
            ->getFactory()
            ->createLookupInstallmentPlansHandler()
            ->lookupInstallmentPlans($installmentPlansRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $this
            ->getFactory()
            ->createOrderSaver()
            ->saveOrderPayment($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function postSaveHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        return $this
            ->getFactory()
            ->createPostSaveHook()
            ->execute($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function authorizePayment(AfterpayCallTransfer $afterpayCallTransfer): AfterpayApiResponseTransfer
    {
        return $this
            ->getFactory()
            ->createAuthorizeTransactionHandler()
            ->authorize($afterpayCallTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return void
     */
    public function capturePayment(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer): void
    {
        $this
            ->getFactory()
            ->createCaptureTransactionHandler()
            ->capture($itemTransfer, $afterpayCallTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function refundPayment(ItemTransfer $itemTransfer, OrderTransfer $orderTransfer): void
    {
        $this
            ->getFactory()
            ->createRefundTransactionHandler()
            ->refund($itemTransfer, $orderTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return void
     */
    public function cancelPayment(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer): void
    {
        $this
            ->getFactory()
            ->createCancelTransactionHandler()
            ->cancel($itemTransfer, $afterpayCallTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder(int $idSalesOrder): AfterpayPaymentTransfer
    {
        return $this
            ->getFactory()
            ->createPaymentReader()
            ->getPaymentByIdSalesOrder($idSalesOrder);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this
            ->getFactory()
            ->createApiAdapter()
            ->getApiVersion();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @return int
     */
    public function getApiStatus(): int
    {
        return $this
            ->getFactory()
            ->createApiAdapter()
            ->getApiStatus();
    }
}
