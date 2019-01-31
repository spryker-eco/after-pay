<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class TransactionLogger implements TransactionLoggerInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface
     */
    protected $utilEncoding;

    /**
     * @param \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface $utilEncoding
     */
    public function __construct(AfterPayToUtilEncodingServiceInterface $utilEncoding)
    {
        $this->utilEncoding = $utilEncoding;
    }

    /**
     * @param string $transactionType
     * @param string $orderReference
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transactionRequest
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $transactionResponse
     *
     * @return void
     */
    public function logTransaction(
        string $transactionType,
        string $orderReference,
        AbstractTransfer $transactionRequest,
        AfterPayApiResponseTransfer $transactionResponse
    ): void {
        $transactionLog = new SpyPaymentAfterPayTransactionLog();
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
    protected function getRequestTransferEncoded(AbstractTransfer $requestTransfer): string
    {
        return $this->utilEncoding->encodeJson($requestTransfer->toArray());
    }
}
