<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Dependency\Facade;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\RefundTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;

class AfterpayToRefundBridge implements AfterpayToRefundInterface
{
    /**
     * @var \Spryker\Zed\Refund\Business\RefundFacadeInterface
     */
    protected $refundFacade;

    /**
     * @param \Spryker\Zed\Refund\Business\RefundFacadeInterface $refundFacade
     */
    public function __construct($refundFacade)
    {
        $this->refundFacade = $refundFacade;
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $salesOrderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $salesOrderEntity
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function calculateRefund(array $salesOrderItems, SpySalesOrder $salesOrderEntity)
    {
        return $this->refundFacade->calculateRefund($salesOrderItems, $salesOrderEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] array $salesOrderItems
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function calculateRefundableItemAmount(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItems)
    {
        return $this->refundFacade->calculateRefundableItemAmount($refundTransfer, $orderTransfer, $salesOrderItems);
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] array $salesOrderItems
     *
     * @return \Generated\Shared\Transfer\RefundTransfer
     */
    public function calculateRefundableExpenseAmount(RefundTransfer $refundTransfer, OrderTransfer $orderTransfer, array $salesOrderItems)
    {
        return $this->refundFacade->calculateRefundableExpenseAmount($refundTransfer, $orderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\RefundTransfer $refundTransfer
     *
     * @return bool
     */
    public function saveRefund(RefundTransfer $refundTransfer)
    {
        return $this->refundFacade->saveRefund($refundTransfer);
    }
}
