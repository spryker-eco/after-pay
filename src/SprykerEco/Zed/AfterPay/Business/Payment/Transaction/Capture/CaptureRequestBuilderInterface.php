<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\ItemTransfer;

interface CaptureRequestBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    public function buildBaseCaptureRequestForOrder(AfterPayCallTransfer $afterPayCallTransfer): AfterPayCaptureRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToCaptureRequest(
        ItemTransfer $orderItemTransfer,
        AfterPayCaptureRequestTransfer $captureRequestTransfer
    );

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToCaptureRequest(
        $expenseAmount,
        AfterPayCaptureRequestTransfer $captureRequestTransfer
    );
}
