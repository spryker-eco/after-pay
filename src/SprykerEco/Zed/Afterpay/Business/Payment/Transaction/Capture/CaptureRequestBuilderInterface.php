<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Capture;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;

interface CaptureRequestBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer
     */
    public function buildBaseCaptureRequestForOrder(AfterpayCallTransfer $afterpayCallTransfer): AfterpayCaptureRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToCaptureRequest(
        ItemTransfer $orderItemTransfer,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    );

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToCaptureRequest(
        $expenseAmount,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    );
}
