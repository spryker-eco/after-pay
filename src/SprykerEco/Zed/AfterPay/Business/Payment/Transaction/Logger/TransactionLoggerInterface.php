<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface TransactionLoggerInterface
{
    /**
     * @param string $transactionType
     * @param string $orderReference
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $transactionRequest
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $transactionResponse
     *
     * @return void
     */
    public function logTransaction(
        string $transactionType,
        string $orderReference,
        AbstractTransfer $transactionRequest,
        AfterPayApiResponseTransfer $transactionResponse
    ): void;
}
