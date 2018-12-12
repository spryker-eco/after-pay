<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCancelResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class CancelTransaction implements CancelTransactionInterface
{
    public const TRANSACTION_TYPE = AfterPayConfig::TRANSACTION_TYPE_CANCEL;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface
     */
    protected $transactionLogger;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface $transactionLogger
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface $apiAdapter
     */
    public function __construct(
        TransactionLoggerInterface $transactionLogger,
        AdapterInterface $apiAdapter
    ) {
        $this->transactionLogger = $transactionLogger;
        $this->apiAdapter = $apiAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelResponseTransfer
     */
    public function executeTransaction(AfterPayCancelRequestTransfer $cancelRequestTransfer): AfterPayCancelResponseTransfer
    {
        $cancelResponseTransfer = $this->apiAdapter->sendCancelRequest($cancelRequestTransfer);
        $this->logTransaction($cancelRequestTransfer, $cancelResponseTransfer);

        return $cancelResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCancelResponseTransfer $cancelResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterPayCancelRequestTransfer $cancelRequestTransfer,
        AfterPayCancelResponseTransfer $cancelResponseTransfer
    ): void {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $cancelRequestTransfer->getOrderNumber(),
            $cancelRequestTransfer,
            $cancelResponseTransfer->getApiResponse()
        );
    }
}
