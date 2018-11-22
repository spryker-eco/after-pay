<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;

interface CancelRequestBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelRequestTransfer
     */
    public function buildBaseCancelRequestForOrder(AfterpayCallTransfer $afterpayCallTransfer);

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
