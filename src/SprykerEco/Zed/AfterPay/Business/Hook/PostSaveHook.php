<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Hook;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig as SharedAfterPayConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\TransactionLogReaderInterface;

class PostSaveHook implements PostSaveHookInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\TransactionLogReaderInterface
     */
    protected $transactionLogReader;

    /**
     * @var \SprykerEco\Zed\AfterPay\AfterPayConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\TransactionLogReaderInterface $transactionLogReader
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        TransactionLogReaderInterface $transactionLogReader,
        AfterPayConfig $config
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
        if (!$this->isAfterPayPaymentProvider($quoteTransfer) || $this->isPaymentAuthorizationSuccessful($quoteTransfer->getOrderReference())) {
            return $checkoutResponseTransfer;
        }

        $this->setPaymentFailedRedirect($checkoutResponseTransfer);

        return $checkoutResponseTransfer;
    }

    /**
     * @param string $orderReference
     *
     * @return bool
     */
    protected function isPaymentAuthorizationSuccessful(string $orderReference): bool
    {
        $transactionLogTransfer = $this->transactionLogReader
            ->findOrderAuthorizeTransactionLogByIdSalesOrder($orderReference);

        if (!$transactionLogTransfer) {
            return false;
        }

        return $transactionLogTransfer->getOutcome() === SharedAfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED;
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

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function isAfterPayPaymentProvider(QuoteTransfer $quoteTransfer): bool
    {
        return $quoteTransfer->getPayment()->getPaymentProvider() === SharedAfterPayConfig::PROVIDER_NAME;
    }
}
