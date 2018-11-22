<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Order;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItem;
use Spryker\Zed\PropelOrm\Business\Transaction\DatabaseTransactionHandlerTrait;
use SprykerEco\Zed\Afterpay\AfterpayConfig;

class Saver implements SaverInterface
{
    use DatabaseTransactionHandlerTrait;

    /**
     * @var \SprykerEco\Zed\Afterpay\AfterpayConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(AfterpayConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $this->handleDatabaseTransaction(function () use ($quoteTransfer, $checkoutResponseTransfer) {
            $this->executeSavePaymentForOrderAndItemsTransaction($quoteTransfer, $checkoutResponseTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    protected function executeSavePaymentForOrderAndItemsTransaction(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) {

        $paymentEntity = $this->buildPaymentEntity($quoteTransfer, $checkoutResponseTransfer);
        $paymentEntity->save();

        $idPayment = $paymentEntity->getIdPaymentAfterpay();

        foreach ($checkoutResponseTransfer->getSaveOrder()->getOrderItems() as $orderItem) {
            $this->savePaymentForOrderItem($orderItem, $idPayment);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param int $idPayment
     *
     * @return void
     */
    protected function savePaymentForOrderItem(ItemTransfer $orderItemTransfer, $idPayment)
    {
        $paymentOrderItemEntity = new SpyPaymentAfterpayOrderItem();
        $paymentOrderItemEntity
            ->setFkPaymentAfterpay($idPayment)
            ->setFkSalesOrderItem($orderItemTransfer->getIdSalesOrderItem());

        $paymentOrderItemEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay
     */
    protected function buildPaymentEntity(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ) {
        $paymentEntity = new SpyPaymentAfterpay();

        $paymentTransfer = $quoteTransfer->getPayment();

        $paymentEntity
            ->setPaymentMethod($paymentTransfer->getPaymentMethod())
            ->setFkSalesOrder($checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder())
            ->setIdCheckout($paymentTransfer->getAfterpayCheckoutId())
            ->setIdChannel($this->getIdChannel($paymentTransfer->getPaymentMethod()))
            ->setInfoscoreCustomerNumber($paymentTransfer->getAfterpayCustomerNumber())
            ->setExpenseTotal($quoteTransfer->getTotals()->getExpenseTotal())
            ->setGrandTotal($quoteTransfer->getTotals()->getGrandTotal());

        return $paymentEntity;
    }

    /**
     * @param string $paymentMethod
     *
     * @return string
     */
    protected function getIdChannel($paymentMethod)
    {
        return $this->config->getPaymentChannelId($paymentMethod);
    }
}
