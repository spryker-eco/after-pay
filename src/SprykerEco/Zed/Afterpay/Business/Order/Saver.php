<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Order;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItem;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Shared\Afterpay\AfterpayConfig as AfterpayConfig1;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\AfterpayConfig;

class Saver implements SaverInterface
{
    use TransactionTrait;

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
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderPayment(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($quoteTransfer, $saveOrderTransfer) {
            $this->executeSavePaymentForOrderAndItemsTransaction($quoteTransfer, $saveOrderTransfer);
        });
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    protected function executeSavePaymentForOrderAndItemsTransaction(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): void {

        $paymentEntity = $this->buildPaymentEntity($quoteTransfer, $saveOrderTransfer);
        $paymentEntity->save();

        $idPayment = $paymentEntity->getIdPaymentAfterpay();

        foreach ($saveOrderTransfer->getOrderItems() as $orderItem) {
            $this->savePaymentForOrderItem($orderItem, $idPayment);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param int $idPayment
     *
     * @return void
     */
    protected function savePaymentForOrderItem(ItemTransfer $orderItemTransfer, int $idPayment): void
    {
        $paymentOrderItemEntity = new SpyPaymentAfterpayOrderItem();
        $paymentOrderItemEntity
            ->setFkPaymentAfterpay($idPayment)
            ->setFkSalesOrderItem($orderItemTransfer->getIdSalesOrderItem());

        $paymentOrderItemEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay
     */
    protected function buildPaymentEntity(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): SpyPaymentAfterpay {
        $paymentEntity = new SpyPaymentAfterpay();

        $paymentTransfer = $quoteTransfer->getPayment();

        $paymentEntity
            ->setFkSalesPayment($paymentTransfer->getIdSalesPayment())
            ->setPaymentMethod($paymentTransfer->getPaymentMethod())
            ->setFkSalesOrder($saveOrderTransfer->getIdSalesOrder())
            ->setIdCheckout($paymentTransfer->getAfterpayCheckoutId())
            ->setIdChannel($this->getIdChannel($paymentTransfer->getPaymentMethod()))
            ->setInfoscoreCustomerNumber($paymentTransfer->getAfterpayCustomerNumber())
            ->setExpenseTotal($quoteTransfer->getTotals()->getExpenseTotal())
            ->setGrandTotal($this->getPaymentPriceToPay($quoteTransfer));

        return $paymentEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return int
     */
    protected function getPaymentPriceToPay(QuoteTransfer $quoteTransfer): int
    {
        if ($quoteTransfer->getPayment() && $quoteTransfer->getPayment()->getAmount()) {
            return $quoteTransfer->getPayment()->getAmount();
        }

        foreach ($quoteTransfer->getPayments() as $paymentTransfer) {
            if ($paymentTransfer->getPaymentMethod() !== AfterpayConfig1::PROVIDER_NAME || !$paymentTransfer->getAmount()) {
                continue;
            }
            return $paymentTransfer->getAmount();
        }

        return $quoteTransfer->getTotals()->getGrandTotal();
    }

    /**
     * @param string $paymentMethod
     *
     * @return string
     */
    protected function getIdChannel(string $paymentMethod): string
    {
        return $this->config->getPaymentChannelId($paymentMethod);
    }
}
