<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayCaptureResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class CaptureTransaction implements CaptureTransactionInterface
{
    public const TRANSACTION_TYPE = AfterPayConfig::TRANSACTION_TYPE_CAPTURE;

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
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer
     */
    public function executeTransaction(AfterPayCaptureRequestTransfer $captureRequestTransfer): AfterPayCaptureResponseTransfer
    {
        $captureResponseTransfer = $this->apiAdapter->sendCaptureRequest($captureRequestTransfer);
        $this->logTransaction($captureRequestTransfer, $captureResponseTransfer);

        return $captureResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer $captureResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterPayCaptureRequestTransfer $captureRequestTransfer,
        AfterPayCaptureResponseTransfer $captureResponseTransfer
    ): void {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $captureRequestTransfer->getOrderNumber(),
            $captureRequestTransfer,
            $captureResponseTransfer->getApiResponse()
        );
    }
}
