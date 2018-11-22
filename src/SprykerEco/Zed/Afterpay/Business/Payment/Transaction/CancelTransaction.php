<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class CancelTransaction implements CancelTransactionInterface
{
    const TRANSACTION_TYPE = AfterpayConstants::TRANSACTION_TYPE_CANCEL;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface
     */
    protected $transactionLogger;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface $transactionLogger
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface $apiAdapter
     */
    public function __construct(
        TransactionLoggerInterface $transactionLogger,
        AdapterInterface $apiAdapter
    ) {
        $this->transactionLogger = $transactionLogger;
        $this->apiAdapter = $apiAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    public function executeTransaction(AfterpayCancelRequestTransfer $cancelRequestTransfer)
    {
        $cancelResponseTransfer = $this->apiAdapter->sendCancelRequest($cancelRequestTransfer);
        $this->logTransaction($cancelRequestTransfer, $cancelResponseTransfer);

        return $cancelResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelResponseTransfer $cancelResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterpayCancelRequestTransfer $cancelRequestTransfer,
        AfterpayCancelResponseTransfer $cancelResponseTransfer
    ) {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $cancelRequestTransfer->getIdSalesOrder(),
            $cancelRequestTransfer,
            $cancelResponseTransfer->getApiResponse()
        );
    }
}
