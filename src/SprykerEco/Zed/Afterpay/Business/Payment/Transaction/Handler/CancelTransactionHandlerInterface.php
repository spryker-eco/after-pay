<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\ItemTransfer;

interface CancelTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return void
     */
    public function cancel(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer): void;
}
