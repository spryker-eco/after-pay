<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace SprykerEco\Zed\Afterpay\Business\Hook;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
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
    public function __construct(TransactionLogReaderInterface $transactionLogReader, AfterpayConfig $config)
    {
        $this->transactionLogReader = $transactionLogReader;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CheckoutResponseTransfer
     */
    public function execute(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer)
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
    protected function isPaymentAuthorizationSuccessful($idSalesOrder)
    {
        $transactionLogTransfer = $this->transactionLogReader
            ->findOrderAuthorizeTransactionLogByIdSalesOrder($idSalesOrder);

        if (!$transactionLogTransfer) {
            return false;
        }

        return $transactionLogTransfer->getOutcome() === AfterpayConstants::API_TRANSACTION_OUTCOME_ACCEPTED;
    }

    /**
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    protected function setPaymentFailedRedirect(CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $paymentFailedUrl = $this->config->getPaymentAuthorizationFailedUrl();

        $checkoutResponseTransfer
            ->setIsExternalRedirect(true)
            ->setRedirectUrl($paymentFailedUrl);
    }
}
