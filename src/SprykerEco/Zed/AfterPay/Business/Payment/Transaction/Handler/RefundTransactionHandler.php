<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterPayPaymentOrderItemTransfer;
use Generated\Shared\Transfer\AfterPayPaymentTransfer;
use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRefundResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\RefundTransactionInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;

class RefundTransactionHandler implements RefundTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\RefundTransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface
     */
    protected $paymentReader;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    protected $refundRequestBuilder;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface
     */
    protected $paymentWriter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\RefundTransactionInterface $transaction
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface $paymentReader
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface $paymentWriter
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $moneyFacade
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface $refundRequestBuilder
     */
    public function __construct(
        RefundTransactionInterface $transaction,
        PaymentReaderInterface $paymentReader,
        PaymentWriterInterface $paymentWriter,
        AfterPayToMoneyFacadeInterface $moneyFacade,
        RefundRequestBuilderInterface $refundRequestBuilder
    ) {
        $this->transaction = $transaction;
        $this->paymentReader = $paymentReader;
        $this->refundRequestBuilder = $refundRequestBuilder;
        $this->moneyFacade = $moneyFacade;
        $this->paymentWriter = $paymentWriter;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function refund(array $items, OrderTransfer $orderTransfer): void
    {
        $refundRequestTransfer = $this->buildRefundRequestForOrderItem($items, $orderTransfer);
        $paymentTransfer = $this->getPaymentTransferForItem($refundRequestTransfer);

        if ($paymentTransfer->getExpenseTotal()) {
            $this->processExpensesRefund($items, $paymentTransfer, $orderTransfer);
        }

        $refundResponseTransfer = $this->transaction->executeTransaction($refundRequestTransfer);

        $this->updateOrderPayment($refundResponseTransfer, $refundRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function processExpensesRefund(
        array $items,
        AfterPayPaymentTransfer $paymentTransfer,
        OrderTransfer $orderTransfer
    ): void {
        if (!$this->isLastItemToRefund($items, $paymentTransfer)) {
            return;
        }

        $expensesRefundRequest = $this->refundRequestBuilder
            ->buildBaseRefundRequestForOrder($orderTransfer);
        $this->addExpensesToRefundRequest($paymentTransfer->getExpenseTotal(), $expensesRefundRequest);
        $expensesRefundRequest->setCaptureNumber($paymentTransfer->getExpensesCaptureNumber());
        $expensesRefundResponse = $this->transaction->executeTransaction($expensesRefundRequest);
        $this->updateOrderPayment($expensesRefundResponse, $expensesRefundRequest);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundRequestTransfer
     */
    protected function buildRefundRequestForOrderItem(
        array $items,
        OrderTransfer $orderTransfer
    ): AfterPayRefundRequestTransfer {
        $refundRequestTransfer = $this->refundRequestBuilder->buildBaseRefundRequestForOrder($orderTransfer);

        foreach ($items as $itemTransfer) {
            $this->refundRequestBuilder->addOrderItemToRefundRequest($itemTransfer, $refundRequestTransfer);
            $this->addCaptureNumberToRefundRequest($refundRequestTransfer, $itemTransfer);
        }

        return $refundRequestTransfer;
    }

    /**
     * @param int $expenseTotal
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return void
     */
    protected function addExpensesToRefundRequest(
        int $expenseTotal,
        AfterPayRefundRequestTransfer $refundRequestTransfer
    ): void {
        $this->refundRequestBuilder->addOrderExpenseToRefundRequest($expenseTotal, $refundRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    protected function getPaymentTransferForItem(AfterPayRefundRequestTransfer $refundRequestTransfer): AfterPayPaymentTransfer
    {
        return $this->paymentReader->getPaymentByIdSalesOrder($refundRequestTransfer->getIdSalesOrder());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentOrderItemTransfer
     */
    protected function getPaymentOrderItemTransferForItem(
        AfterPayRefundRequestTransfer $refundRequestTransfer,
        ItemTransfer $itemTransfer
    ): AfterPayPaymentOrderItemTransfer {
        $paymentTransfer = $this->getPaymentTransferForItem($refundRequestTransfer);

        return $this->paymentReader
            ->getPaymentOrderItemByIdSalesOrderItemAndIdPayment(
                $itemTransfer->getIdSalesOrderItem(),
                $paymentTransfer->getIdPaymentAfterPay()
            );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundResponseTransfer $refundResponseTransfer
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return void
     */
    protected function updateOrderPayment(
        AfterPayRefundResponseTransfer $refundResponseTransfer,
        AfterPayRefundRequestTransfer $refundRequestTransfer
    ): void {
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
            $refundRequestTransfer->getIdSalesOrder()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $items
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     *
     * @return bool
     */
    protected function isLastItemToRefund(array $items, AfterPayPaymentTransfer $paymentTransfer): bool
    {
        $refundable = $paymentTransfer->getAuthorizedTotal() -
            $paymentTransfer->getCancelledTotal() -
            $paymentTransfer->getRefundedTotal() -
            $paymentTransfer->getExpenseTotal();

        $amountToRefund = 0;
        foreach ($items as $itemTransfer) {
            $amountToRefund += $itemTransfer->getRefundableAmount();
        }

        return $refundable === $amountToRefund;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return void
     */
    protected function addCaptureNumberToRefundRequest(
        AfterPayRefundRequestTransfer $refundRequestTransfer,
        ItemTransfer $itemTransfer
    ): void {
        $paymentOrderItemTransfer = $this->getPaymentOrderItemTransferForItem($refundRequestTransfer, $itemTransfer);
        $refundRequestTransfer->setCaptureNumber($paymentOrderItemTransfer->getCaptureNumber());
    }
}
