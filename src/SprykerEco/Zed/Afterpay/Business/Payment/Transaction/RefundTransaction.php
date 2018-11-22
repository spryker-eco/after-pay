<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class RefundTransaction implements RefundTransactionInterface
{
    const TRANSACTION_TYPE = AfterpayConstants::TRANSACTION_TYPE_REFUND;

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
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    public function executeTransaction(AfterpayRefundRequestTransfer $refundRequestTransfer)
    {
        $refundResponseTransfer = $this->apiAdapter->sendRefundRequest($refundRequestTransfer);
        $this->logTransaction($refundRequestTransfer, $refundResponseTransfer);

        return $refundResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayRefundResponseTransfer $refundResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterpayRefundRequestTransfer $refundRequestTransfer,
        AfterpayRefundResponseTransfer $refundResponseTransfer
    ) {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $refundRequestTransfer->getIdSalesOrder(),
            $refundRequestTransfer,
            $refundResponseTransfer->getApiResponse()
        );
    }
}
