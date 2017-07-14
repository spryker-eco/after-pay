<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Command;

use Generated\Shared\Transfer\ItemTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \SprykerEco\Zed\Afterpay\Business\AfterpayFacade getFacade()
 * @method \SprykerEco\Zed\Afterpay\Communication\AfterpayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainer getQueryContainer()
 */
class CancelPlugin extends AbstractPlugin implements CommandByOrderInterface
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $orderTransfer = $this->getOrderTransfer($orderEntity);

        foreach ($orderItems as $orderItem) {
            $itemTransfer = $this->getOrderItemTransfer($orderItem);
            $this->getFacade()->cancelPayment($itemTransfer, $orderTransfer);
        }

        return [];
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function getOrderItemTransfer(SpySalesOrderItem $orderItem)
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
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function getOrderTransfer(SpySalesOrder $order)
    {
        $orderTransfer = $this
            ->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder(
                $order->getIdSalesOrder()
            );

        return $orderTransfer;
    }

}
