<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;

class AuthorizeTransaction implements AuthorizeTransactionInterface
{
    public const TRANSACTION_TYPE = AfterpayConfig::TRANSACTION_TYPE_AUTHORIZE;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface
     */
    protected $transactionLogger;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface
     */
    protected $paymentAuthorizeWriter;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface $transactionLogger
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface $apiAdapter
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface $paymentAuthorizeWriter
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
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function executeTransaction(AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer): AfterpayApiResponseTransfer
    {
        $authorizeResponseTransfer = $this->apiAdapter->sendAuthorizationRequest($authorizeRequestTransfer);
        $this->logTransaction($authorizeRequestTransfer, $authorizeResponseTransfer);
        $this->writeAuthorizeResponse($authorizeRequestTransfer, $authorizeResponseTransfer);

        return $authorizeResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayApiResponseTransfer $authorizeResponseTransfer
     *
     * @return void
     */
    protected function logTransaction(
        AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer,
        AfterpayApiResponseTransfer $authorizeResponseTransfer
    ): void {
        $this->transactionLogger->logTransaction(
            static::TRANSACTION_TYPE,
            $authorizeRequestTransfer->getOrder()->getNumber(),
            $authorizeRequestTransfer,
            $authorizeResponseTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayApiResponseTransfer $authorizeResponseTransfer
     *
     * @return void
     */
    protected function writeAuthorizeResponse(
        AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer,
        AfterpayApiResponseTransfer $authorizeResponseTransfer
    ): void {
        $this->paymentAuthorizeWriter->save(
            $authorizeRequestTransfer->getOrder()->getNumber(),
            $authorizeResponseTransfer->getReservationId(),
            $authorizeResponseTransfer->getCheckoutId()
        );
    }
}
