<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface TransactionLoggerInterface
{
    /**
     * @param string $transactionType
     * @param string $orderReference
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $transactionRequest
     * @param \Generated\Shared\Transfer\AfterpayApiResponseTransfer $transactionResponse
     *
     * @return void
     */
    public function logTransaction(
        $transactionType,
        $orderReference,
        AbstractTransfer $transactionRequest,
        AfterpayApiResponseTransfer $transactionResponse
    );
}
