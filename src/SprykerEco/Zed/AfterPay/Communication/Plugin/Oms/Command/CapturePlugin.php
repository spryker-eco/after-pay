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
     * {@inheritDoc}
     * - Sends payment `capture` request to AfterPay gateway, to capture payment for a specific order item.
     * - If it is the first item `capture` request for given order, captures also full expense amount.
     * - Saves the transaction result in DB and updates payment with new total captured amount.
     *
     * @api
     *
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $orderItems
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
     * @param array<\Orm\Zed\Sales\Persistence\SpySalesOrderItem> $orderItems
     *
     * @return array<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected function getItemTransfers(array $orderItems): array
    {
        $items = [];

        foreach ($orderItems as $orderItem) {
            $items[] = (new ItemTransfer())
                ->fromArray($orderItem->toArray(), true)
                ->setUnitGrossPrice($orderItem->getGrossPrice())
                ->setUnitNetPrice($orderItem->getNetPrice())
                ->setUnitPriceToPayAggregation($orderItem->getPriceToPayAggregation())
                ->setUnitTaxAmountFullAggregation($orderItem->getTaxAmountFullAggregation());
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
