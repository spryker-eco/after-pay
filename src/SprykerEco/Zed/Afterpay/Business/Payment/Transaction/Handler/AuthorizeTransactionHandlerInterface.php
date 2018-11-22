<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterpayCallTransfer;

interface AuthorizeTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function authorize(AfterpayCallTransfer $orderTransfer);
}
