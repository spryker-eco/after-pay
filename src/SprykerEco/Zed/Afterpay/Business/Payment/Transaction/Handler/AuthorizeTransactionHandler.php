<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransactionInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface;

class AuthorizeTransactionHandler implements AuthorizeTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface
     */
    protected $paymentWriter;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    protected $priceToPayProvider;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransactionInterface $transaction
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface $requestBuilder
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface $paymentWriter
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface $priceToPayProvider
     */
    public function __construct(
        AuthorizeTransactionInterface $transaction,
        AuthorizeRequestBuilderInterface $requestBuilder,
        PaymentWriterInterface $paymentWriter,
        PriceToPayProviderInterface $priceToPayProvider
    ) {
        $this->transaction = $transaction;
        $this->requestBuilder = $requestBuilder;
        $this->paymentWriter = $paymentWriter;
        $this->priceToPayProvider = $priceToPayProvider;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function authorize(AfterpayCallTransfer $afterpayCallTransfer): AfterpayApiResponseTransfer
    {
        $authorizeRequestTransfer = $this->buildAuthorizeRequest($afterpayCallTransfer);

        return $this->transaction->executeTransaction($authorizeRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    protected function buildAuthorizeRequest(AfterpayCallTransfer $afterpayCallTransfer): AfterpayAuthorizeRequestTransfer
    {
        $authorizeRequestTransfer = $this->requestBuilder->buildAuthorizeRequest($afterpayCallTransfer);

        return $authorizeRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayApiResponseTransfer $authorizeResponseTransfer
     *
     * @return void
     */
    protected function setPaymentReservationId(
        AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer,
        AfterpayApiResponseTransfer $authorizeResponseTransfer
    ): void {
        $this->paymentWriter->setIdReservationByIdSalesOrder(
            $authorizeResponseTransfer->getReservationId(),
            $authorizeRequestTransfer->getIdSalesOrder()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function setPaymentTotalAuthorizedAmount(OrderTransfer $orderTransfer): void
    {
        $this->paymentWriter->setAuthorizedTotalByIdSalesOrder(
            $this->priceToPayProvider->getPriceToPayForOrder($orderTransfer),
            $orderTransfer->getIdSalesOrder()
        );
    }
}
