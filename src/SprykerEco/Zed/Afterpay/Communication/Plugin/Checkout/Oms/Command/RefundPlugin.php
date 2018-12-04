<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Command;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \SprykerEco\Zed\Afterpay\Business\AfterpayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Afterpay\Communication\AfterpayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainer getQueryContainer()
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 */
class RefundPlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * Command which is executed per order basis
     *
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $orderTransfer = $this->getOrderTransfer($orderEntity);
        $this->hydrateAfterpayPayment($orderTransfer);

        foreach ($orderItems as $orderItem) {
            $itemTransfer = $this->getOrderItemTransfer($orderItem);
            $this->getFacade()->refundPayment($itemTransfer, $orderTransfer);
        }
        $this->storeRefund($orderItems, $orderEntity);

        return [];
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function getOrderTransfer(SpySalesOrder $order): OrderTransfer
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $order->getIdSalesOrder()
            );

        return $orderTransfer;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay|null
     */
    protected function getPaymentEntity(SpySalesOrder $orderEntity): ?SpyPaymentAfterpay
    {
        return $orderEntity->getSpyPaymentAfterpays()->getFirst();
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function getOrderItemTransfer(SpySalesOrderItem $orderItem): ItemTransfer
    {
        $itemTransfer = new ItemTransfer();
        $itemTransfer->fromArray($orderItem->toArray(), true);

        $itemTransfer->setUnitGrossPrice($orderItem->getGrossPrice());
        $itemTransfer->setUnitNetPrice($orderItem->getNetPrice());

        $itemTransfer->setUnitPriceToPayAggregation($orderItem->getPriceToPayAggregation());
        $itemTransfer->setUnitTaxAmountFullAggregation($orderItem->getTaxAmountFullAggregation());

        return $itemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function hydrateAfterpayPayment(OrderTransfer $orderTransfer): OrderTransfer
    {
        $paymentTransfer = $this->getFacade()->getPaymentByIdSalesOrder($orderTransfer->getIdSalesOrder());
        $orderTransfer->setAfterpayPayment($paymentTransfer);

        return $orderTransfer;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return void
     */
    protected function storeRefund(array $orderItems, SpySalesOrder $orderEntity): void
    {
        $refundTransfer = $this->getFactory()->getRefundFacade()->calculateRefund($orderItems, $orderEntity);
        $this->getFactory()->getRefundFacade()->saveRefund($refundTransfer);
    }
}
