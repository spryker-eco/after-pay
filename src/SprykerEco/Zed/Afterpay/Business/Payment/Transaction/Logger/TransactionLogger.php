<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class TransactionLogger implements TransactionLoggerInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface
     */
    protected $utilEncoding;

    /**
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface $utilEncoding
     */
    public function __construct(AfterpayToUtilEncodingInterface $utilEncoding)
    {
        $this->utilEncoding = $utilEncoding;
    }

    /**
     * @param string $transactionType
     * @param string $orderReference
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transactionRequest
     * @param \Generated\Shared\Transfer\AfterpayApiResponseTransfer $transactionResponse
     *
     * @return void
     */
    public function logTransaction(
        $transactionType,
        $orderReference,
        AbstractTransfer $transactionRequest,
        AfterpayApiResponseTransfer $transactionResponse
    ) {
        $transactionLog = new SpyPaymentAfterpayTransactionLog();
        $transactionLog
            ->setOrderReference($orderReference)
            ->setTransactionType($transactionType)
            ->setOutcome($transactionResponse->getOutcome())
            ->setRequestPayload($this->getRequestTransferEncoded($transactionRequest))
            ->setResponsePayload($transactionResponse->getResponsePayload())
            ->save();
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $requestTransfer
     *
     * @return string
     */
    protected function getRequestTransferEncoded(AbstractTransfer $requestTransfer)
    {
        return $this->utilEncoding->encodeJson($requestTransfer->toArray());
    }
}
