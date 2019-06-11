<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\AuthorizeTransactionInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface;

class AuthorizeTransactionHandler implements AuthorizeTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\AuthorizeTransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    protected $requestBuilder;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface
     */
    protected $paymentWriter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    protected $priceToPayProvider;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\AuthorizeTransactionInterface $transaction
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface $requestBuilder
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface $paymentWriter
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface $priceToPayProvider
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
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    public function authorize(AfterPayCallTransfer $afterPayCallTransfer): AfterPayApiResponseTransfer
    {
        $authorizeRequestTransfer = $this->buildAuthorizeRequest($afterPayCallTransfer);
        $authorizeResponseTransfer = $this->transaction->executeTransaction($authorizeRequestTransfer);

        $this->setPaymentReservationId($afterPayCallTransfer, $authorizeResponseTransfer);
        $this->setPaymentTotalAuthorizedAmount($afterPayCallTransfer);

        if ($authorizeResponseTransfer->getCustomerNumber()) {
            $this->setInfoscoreCustomerNumber($afterPayCallTransfer, $authorizeResponseTransfer);
        }

        return $authorizeResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer
     */
    protected function buildAuthorizeRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayAuthorizeRequestTransfer
    {
        $authorizeRequestTransfer = $this->requestBuilder->buildAuthorizeRequest($afterPayCallTransfer);

        return $authorizeRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $authorizeResponseTransfer
     *
     * @return void
     */
    protected function setPaymentReservationId(
        AfterPayCallTransfer $afterPayCallTransfer,
        AfterPayApiResponseTransfer $authorizeResponseTransfer
    ): void {
        $this->paymentWriter->setIdReservationByIdSalesOrder(
            $authorizeResponseTransfer->getReservationId(),
            $afterPayCallTransfer->getIdSalesOrder()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $authorizeResponseTransfer
     *
     * @return void
     */
    protected function setInfoscoreCustomerNumber(
        AfterPayCallTransfer $afterPayCallTransfer,
        AfterPayApiResponseTransfer $authorizeResponseTransfer
    ): void {
        $this->paymentWriter->setCustomerNumberByIdSalesOrder(
            $authorizeResponseTransfer->getCustomerNumber(),
            $afterPayCallTransfer->getIdSalesOrder()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    protected function setPaymentTotalAuthorizedAmount(AfterPayCallTransfer $afterPayCallTransfer): void
    {
        $this->paymentWriter->setAuthorizedTotalByIdSalesOrder(
            $this->priceToPayProvider->getPriceToPayForOrder($afterPayCallTransfer),
            $afterPayCallTransfer->getIdSalesOrder()
        );
    }
}
