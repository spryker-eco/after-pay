<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterpayPaymentTransfer;
use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransactionInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;

class RefundTransactionHandler implements RefundTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface
     */
    protected $paymentReader;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    private $refundRequestBuilder;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface
     */
    private $paymentWriter;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    protected $moneyFacade;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransactionInterface $transaction
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface $paymentReader
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface $paymentWriter
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $moneyFacade
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface $refundRequestBuilder
     */
    public function __construct(
        RefundTransactionInterface $transaction,
        PaymentReaderInterface $paymentReader,
        PaymentWriterInterface $paymentWriter,
        AfterpayToMoneyInterface $moneyFacade,
        RefundRequestBuilderInterface $refundRequestBuilder
    ) {
        $this->transaction = $transaction;
        $this->paymentReader = $paymentReader;
        $this->refundRequestBuilder = $refundRequestBuilder;
        $this->moneyFacade = $moneyFacade;
        $this->paymentWriter = $paymentWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function refund(ItemTransfer $itemTransfer, OrderTransfer $orderTransfer)
    {
        $refundRequestTransfer = $this->buildRefundRequestForOrderItem($itemTransfer, $orderTransfer);
        $paymentTransfer = $this->getPaymentTransferForItem($itemTransfer);

        $this->processExpensesRefund($itemTransfer, $paymentTransfer, $orderTransfer);

        $refundResponseTransfer = $this->transaction->executeTransaction($refundRequestTransfer);

        $this->updateOrderPayment(
            $refundResponseTransfer,
            $refundRequestTransfer,
            $orderTransfer->getIdSalesOrder()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayPaymentTransfer $paymentTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function processExpensesRefund(ItemTransfer $itemTransfer, AfterpayPaymentTransfer $paymentTransfer, OrderTransfer $orderTransfer)
    {
        if (!$this->isLastItemToRefund($itemTransfer, $paymentTransfer)) {
            return;
        }
        $expensesRefundRequest = $this->refundRequestBuilder
            ->buildBaseRefundRequestForOrder($orderTransfer);
        $this->addExpensesToRefundRequest($paymentTransfer->getExpenseTotal(), $expensesRefundRequest);
        $expensesRefundRequest->setCaptureNumber($paymentTransfer->getExpensesCaptureNumber());
        $expensesRefundResponse = $this->transaction->executeTransaction($expensesRefundRequest);
        $this->updateOrderPayment($expensesRefundResponse, $expensesRefundRequest, $orderTransfer->getIdSalesOrder());
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundRequestTransfer
     */
    protected function buildRefundRequestForOrderItem(ItemTransfer $itemTransfer, OrderTransfer $orderTransfer)
    {
        $refundRequestTransfer = $this->refundRequestBuilder
            ->buildBaseRefundRequestForOrder($orderTransfer);
        $this->refundRequestBuilder
            ->addOrderItemToRefundRequest(
                $itemTransfer,
                $refundRequestTransfer
            );
        $this->addCaptureNumberToRefundRequest($refundRequestTransfer, $itemTransfer);

        return $refundRequestTransfer;
    }

    /**
     * @param int $expenseTotal
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return void
     */
    protected function addExpensesToRefundRequest(
        $expenseTotal,
        AfterpayRefundRequestTransfer $refundRequestTransfer
    ) {
        $this->refundRequestBuilder
            ->addOrderExpenseToRefundRequest(
                $expenseTotal,
                $refundRequestTransfer
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentTransfer
     */
    protected function getPaymentTransferForItem(ItemTransfer $itemTransfer)
    {
        return $this->paymentReader
            ->getPaymentByIdSalesOrder(
                $itemTransfer->getFkSalesOrder()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentOrderItemTransfer
     */
    protected function getPaymentOrderItemTransferForItem(ItemTransfer $itemTransfer)
    {
        $paymentTransfer = $this->getPaymentTransferForItem($itemTransfer);
        return $this->paymentReader
            ->getPaymentOrderItemByIdSalesOrderItemAndIdPayment(
                $itemTransfer->getIdSalesOrderItem(),
                $paymentTransfer->getIdPaymentAfterpay()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundResponseTransfer $refundResponseTransfer
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     * @param int $idSalesOrder
     *
     * @return void
     */
    protected function updateOrderPayment(
        AfterpayRefundResponseTransfer $refundResponseTransfer,
        AfterpayRefundRequestTransfer $refundRequestTransfer,
        $idSalesOrder
    ) {
        if (!$refundResponseTransfer->getTotalCapturedAmount()) {
            return;
        }

        $refundedAmountDecimal = (float)0;
        $refundedAmountInt = $this->moneyFacade->convertDecimalToInteger($refundedAmountDecimal);

        foreach ($refundRequestTransfer->getOrderItems() as $item) {
            $itemGrossPriceDecimal = (float)$item->getGrossUnitPrice();
            $refundedAmountInt += $this->moneyFacade->convertDecimalToInteger($itemGrossPriceDecimal);
        }

        $this->paymentWriter->increaseRefundedTotalByIdSalesOrder(
            $refundedAmountInt,
            $idSalesOrder
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayPaymentTransfer $paymentTransfer
     *
     * @return bool
     */
    protected function isLastItemToRefund($itemTransfer, $paymentTransfer)
    {
        return $paymentTransfer->getAuthorizedTotal() -
            $paymentTransfer->getCancelledTotal() -
            $paymentTransfer->getRefundedTotal() -
            $paymentTransfer->getExpenseTotal() === $itemTransfer->getRefundableAmount();
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return void
     */
    protected function addCaptureNumberToRefundRequest($refundRequestTransfer, $itemTransfer)
    {
        $paymentOrderItemTransfer = $this->getPaymentOrderItemTransferForItem($itemTransfer);
        $refundRequestTransfer->setCaptureNumber($paymentOrderItemTransfer->getCaptureNumber());
    }
}
