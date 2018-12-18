<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface RefundTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function refund(array $items, OrderTransfer $orderTransfer): void;
}
