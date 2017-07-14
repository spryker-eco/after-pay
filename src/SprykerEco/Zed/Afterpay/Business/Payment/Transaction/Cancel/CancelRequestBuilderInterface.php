<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel;

use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface CancelRequestBuilderInterface
{

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelRequestTransfer
     */
    public function buildBaseCancelRequestForOrder(OrderTransfer $orderTransfer);

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToCancelRequest(
        ItemTransfer $orderItemTransfer,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    );

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToCancelRequest(
        $expenseAmount,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    );

}
