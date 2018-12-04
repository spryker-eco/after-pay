<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class AuthorizeTransaction implements AuthorizeTransactionInterface
{
    public const TRANSACTION_TYPE = AfterPayConfig::TRANSACTION_TYPE_AUTHORIZE;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface
     */
    protected $transactionLogger;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface
     */
    protected $paymentAuthorizeWriter;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface $transactionLogger
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface $apiAdapter
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface $paymentAuthorizeWriter
     */
    public function __construct(
        TransactionLoggerInterface $transactionLogger,
        AdapterInterface $apiAdapter,
        PaymentAuthorizeWriterInterface $paymentAuthorizeWriter
    ) {
        $this->transactionLogger = $transactionLogger;
        $this->apiAdapter = $apiAdapter;
        $this->paymentAuthorizeWriter = $paymentAuthorizeWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    public function executeTransaction(AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer): AfterPayApiResponseTransfer
    {
        $authorizeResponseTransfer = $this->apiAdapter->sendAuthorizationRequest($authorizeRequestTransfer);
        $this->logTransaction($authorizeRequestTransfer, $authorizeResponseTransfer);
        $this->writeAuthorizeResponse($authorizeRequestTransfer, $authorizeResponseTransfer);

        return $authorizeResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $authorizeResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer,
        AfterPayApiResponseTransfer $authorizeResponseTransfer
    ): void {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $authorizeRequestTransfer->getOrder()->getNumber(),
            $authorizeRequestTransfer,
            $authorizeResponseTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $authorizeResponseTransfer
     *
     * @return void
     */
    protected function writeAuthorizeResponse(
        AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer,
        AfterPayApiResponseTransfer $authorizeResponseTransfer
    ): void {
        $this->paymentAuthorizeWriter->save(
            $authorizeRequestTransfer->getOrder()->getNumber(),
            $authorizeResponseTransfer->getReservationId(),
            $authorizeResponseTransfer->getCheckoutId()
        );
    }
}
