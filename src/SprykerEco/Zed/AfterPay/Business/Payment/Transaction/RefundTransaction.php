<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRefundResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class RefundTransaction implements RefundTransactionInterface
{
    public const TRANSACTION_TYPE = AfterPayConfig::TRANSACTION_TYPE_REFUND;

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
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundResponseTransfer
     */
    public function executeTransaction(AfterPayRefundRequestTransfer $refundRequestTransfer): AfterPayRefundResponseTransfer
    {
        $refundResponseTransfer = $this->apiAdapter->sendRefundRequest($refundRequestTransfer);
        $this->logTransaction($refundRequestTransfer, $refundResponseTransfer);

        return $refundResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayRefundResponseTransfer $refundResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterPayRefundRequestTransfer $refundRequestTransfer,
        AfterPayRefundResponseTransfer $refundResponseTransfer
    ): void {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $refundRequestTransfer->getOrderNumber(),
            $refundRequestTransfer,
            $refundResponseTransfer->getApiResponse()
        );
    }
}
