<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund;

use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface RefundRequestBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundRequestTransfer
     */
    public function buildBaseRefundRequestForOrder(OrderTransfer $orderTransfer);

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToRefundRequest(
        ItemTransfer $orderItemTransfer,
        AfterpayRefundRequestTransfer $captureRequestTransfer
    );

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToRefundRequest(
        $expenseAmount,
        AfterpayRefundRequestTransfer $refundRequestTransfer
    );
}
