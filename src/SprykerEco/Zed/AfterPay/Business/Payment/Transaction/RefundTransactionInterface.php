<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRefundResponseTransfer;

interface RefundTransactionInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundResponseTransfer
     */
    public function executeTransaction(AfterPayRefundRequestTransfer $refundRequestTransfer): AfterPayRefundResponseTransfer;
}
