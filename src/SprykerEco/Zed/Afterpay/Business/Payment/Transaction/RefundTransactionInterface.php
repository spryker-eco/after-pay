<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundResponseTransfer;

interface RefundTransactionInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    public function executeTransaction(AfterpayRefundRequestTransfer $refundRequestTransfer): AfterpayRefundResponseTransfer;
}
