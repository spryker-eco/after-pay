<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Hook;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig as AfterpayConfig1;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReaderInterface;

class PostSaveHook implements PostSaveHookInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReaderInterface
     */
    private $transactionLogReader;

    /**
     * @var \SprykerEco\Zed\Afterpay\AfterpayConfig
     */
    private $config;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReaderInterface $transactionLogReader
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(
        TransactionLogReaderInterface $transactionLogReader,
        AfterpayConfig $config
    ) {
        $this->transactionLogReader = $transactionLogReader;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): CheckoutResponseTransfer
    {
        $idSalesOrder = $checkoutResponseTransfer->getSaveOrder()->getIdSalesOrder();

        if ($this->isPaymentAuthorizationSuccessful($idSalesOrder)) {
            return $checkoutResponseTransfer;
        }

        $this->setPaymentFailedRedirect($checkoutResponseTransfer);

        return $checkoutResponseTransfer;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    protected function isPaymentAuthorizationSuccessful(int $idSalesOrder): bool
    {
        $transactionLogTransfer = $this->transactionLogReader
            ->findOrderAuthorizeTransactionLogByIdSalesOrder($idSalesOrder);

        if (!$transactionLogTransfer) {
            return false;
        }

        return $transactionLogTransfer->getOutcome() === AfterpayConfig1::API_TRANSACTION_OUTCOME_ACCEPTED;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    protected function setPaymentFailedRedirect(CheckoutResponseTransfer $checkoutResponseTransfer): void
    {
        $paymentFailedUrl = $this->config->getPaymentAuthorizationFailedUrl();

        $checkoutResponseTransfer
            ->setIsExternalRedirect(true)
            ->setRedirectUrl($paymentFailedUrl);
    }
}
