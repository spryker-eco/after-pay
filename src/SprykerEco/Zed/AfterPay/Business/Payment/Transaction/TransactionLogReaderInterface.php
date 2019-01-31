<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayTransactionLogTransfer;

interface TransactionLogReaderInterface
{
    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\AfterPayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeTransactionLogByIdSalesOrder(string $orderReference): ?AfterPayTransactionLogTransfer;
}
