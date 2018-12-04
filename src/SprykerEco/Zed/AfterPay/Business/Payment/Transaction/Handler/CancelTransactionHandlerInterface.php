<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\ItemTransfer;

interface CancelTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    public function cancel(ItemTransfer $itemTransfer, AfterPayCallTransfer $afterPayCallTransfer): void;
}
