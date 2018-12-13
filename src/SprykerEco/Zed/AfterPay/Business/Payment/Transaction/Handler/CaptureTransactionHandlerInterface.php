<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterPayCallTransfer;

interface CaptureTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    public function capture(array $items, AfterPayCallTransfer $afterPayCallTransfer): void;
}
