<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCancelResponseTransfer;

interface CancelTransactionInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelResponseTransfer
     */
    public function executeTransaction(AfterPayCancelRequestTransfer $cancelRequestTransfer): AfterPayCancelResponseTransfer;
}
