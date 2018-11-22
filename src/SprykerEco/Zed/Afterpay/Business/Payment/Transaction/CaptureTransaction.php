<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class CaptureTransaction implements CaptureTransactionInterface
{
    const TRANSACTION_TYPE = AfterpayConstants::TRANSACTION_TYPE_CAPTURE;

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
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    public function executeTransaction(AfterpayCaptureRequestTransfer $captureRequestTransfer)
    {
        $captureResponseTransfer = $this->apiAdapter->sendCaptureRequest($captureRequestTransfer);
        $this->logTransaction($captureRequestTransfer, $captureResponseTransfer);

        return $captureResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer $captureResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterpayCaptureRequestTransfer $captureRequestTransfer,
        AfterpayCaptureResponseTransfer $captureResponseTransfer
    ) {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $captureRequestTransfer->getIdSalesOrder(),
            $captureRequestTransfer,
            $captureResponseTransfer->getApiResponse()
        );
    }
}
