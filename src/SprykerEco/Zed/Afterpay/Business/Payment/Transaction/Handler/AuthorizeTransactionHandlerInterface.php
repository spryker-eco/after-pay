<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;

interface AuthorizeTransactionHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function authorize(AfterpayCallTransfer $orderTransfer): AfterpayApiResponseTransfer;
}
