<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelResponseTransfer;

interface CancelTransactionInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    public function executeTransaction(AfterpayCancelRequestTransfer $cancelRequestTransfer): AfterpayCancelResponseTransfer;
}
