<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureResponseTransfer;

interface CaptureTransactionInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    public function executeTransaction(AfterpayCaptureRequestTransfer $captureRequestTransfer): AfterpayCaptureResponseTransfer;
}
