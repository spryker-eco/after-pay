<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Order;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Shared\AfterPay\AfterPayConfig as SharedAfterPayConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;

class Saver implements SaverInterface
{
    use TransactionTrait;

    /**
     * @var \SprykerEco\Zed\AfterPay\AfterPayConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(AfterPayConfig $config)
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
        if ($quoteTransfer->getPayment()->getPaymentProvider() !== SharedAfterPayConfig::PROVIDER_NAME) {
            return;
        }

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

        $idPayment = $paymentEntity->getIdPaymentAfterPay();

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
        $paymentOrderItemEntity = new SpyPaymentAfterPayOrderItem();
        $paymentOrderItemEntity
            ->setFkPaymentAfterPay($idPayment)
            ->setFkSalesOrderItem($orderItemTransfer->getIdSalesOrderItem());

        $paymentOrderItemEntity->save();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay
     */
    protected function buildPaymentEntity(
        QuoteTransfer $quoteTransfer,
        SaveOrderTransfer $saveOrderTransfer
    ): SpyPaymentAfterPay {
        $paymentEntity = new SpyPaymentAfterPay();

        $paymentTransfer = $quoteTransfer->getPayment();

        $paymentEntity
            ->setFkSalesPayment($paymentTransfer->getIdSalesPayment())
            ->setPaymentMethod($paymentTransfer->getPaymentMethod())
            ->setFkSalesOrder($saveOrderTransfer->getIdSalesOrder())
            ->setIdCheckout($this->getIdCheckout($quoteTransfer))
            ->setIdChannel($this->getIdChannel($paymentTransfer->getPaymentMethod()))
            ->setInfoscoreCustomerNumber($paymentTransfer->getAfterPayCustomerNumber())
            ->setExpenseTotal($quoteTransfer->getTotals()->getExpenseTotal())
            ->setGrandTotal($this->getPaymentPriceToPay($quoteTransfer));

        $quoteTransfer->setOrderReference($saveOrderTransfer->getOrderReference());
        $quoteTransfer->getPayment()->setIdSalesOrder($saveOrderTransfer->getIdSalesOrder());

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
            if ($paymentTransfer->getPaymentMethod() !== SharedAfterPayConfig::PROVIDER_NAME || !$paymentTransfer->getAmount()) {
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

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string|null
     */
    protected function getIdCheckout(QuoteTransfer $quoteTransfer): ?string
    {
        if ($quoteTransfer->getAfterPayAvailablePaymentMethods()) {
            return $quoteTransfer->getAfterPayAvailablePaymentMethods()->getCheckoutId();
        }

        return null;
    }
}
