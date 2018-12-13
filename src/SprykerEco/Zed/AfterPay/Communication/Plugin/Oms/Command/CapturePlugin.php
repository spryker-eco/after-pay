<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Plugin\Oms\Command;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \SprykerEco\Zed\AfterPay\Business\AfterPayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainer getQueryContainer()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 */
class CapturePlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
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
        $afterPayCallTransfer = $this->getFactory()
            ->createOrderToCallConverter()
            ->convert($orderTransfer);

        $items = $this->getItemTransfers($orderItems);

        $this->getFacade()->capturePayment($items, $afterPayCallTransfer);

        return [];
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function getItemTransfers(array $orderItems): array
    {
        $items = [];

        foreach ($orderItems as $orderItem) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->fromArray($orderItem->toArray(), true);
            $itemTransfer->setUnitGrossPrice($orderItem->getGrossPrice());
            $itemTransfer->setUnitNetPrice($orderItem->getNetPrice());
            $itemTransfer->setUnitPriceToPayAggregation($orderItem->getPriceToPayAggregation());
            $itemTransfer->setUnitTaxAmountFullAggregation($orderItem->getTaxAmountFullAggregation());
            $items[] = $itemTransfer;
        }

        return $items;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $order
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function getOrderTransfer(SpySalesOrder $order): OrderTransfer
    {
        return $this->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($order->getIdSalesOrder());
    }
}
