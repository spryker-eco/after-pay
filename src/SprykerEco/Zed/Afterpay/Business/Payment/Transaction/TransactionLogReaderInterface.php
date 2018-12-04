<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayTransactionLogTransfer;

interface TransactionLogReaderInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterpayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeTransactionLogByIdSalesOrder(int $idSalesOrder): ?AfterpayTransactionLogTransfer;
}
