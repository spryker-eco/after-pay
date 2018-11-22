<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelResponseTransfer;
use Generated\Shared\Transfer\AfterpayPaymentTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CancelTransactionInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;

class CancelTransactionHandler implements CancelTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CancelTransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface
     */
    protected $paymentReader;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface
     */
    private $cancelRequestBuilder;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface
     */
    private $paymentWriter;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    private $money;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CancelTransactionInterface $transaction
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface $paymentReader
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface $paymentWriter
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $money
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface $cancelRequestBuilder
     */
    public function __construct(
        CancelTransactionInterface $transaction,
        PaymentReaderInterface $paymentReader,
        PaymentWriterInterface $paymentWriter,
        AfterpayToMoneyInterface $money,
        CancelRequestBuilderInterface $cancelRequestBuilder
    ) {
        $this->transaction = $transaction;
        $this->paymentReader = $paymentReader;
        $this->cancelRequestBuilder = $cancelRequestBuilder;
        $this->paymentWriter = $paymentWriter;
        $this->money = $money;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return void
     */
    public function cancel(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer)
    {
        $cancelRequestTransfer = $this->buildCancelRequestForOrderItem($itemTransfer, $afterpayCallTransfer);
        $paymentTransfer = $this->getPaymentTransferForItem($itemTransfer);

        if ($this->isExpenseShouldBeCancelled($cancelRequestTransfer, $paymentTransfer)) {
            $this->addExpensesToCancelRequest($paymentTransfer->getExpenseTotal(), $cancelRequestTransfer);
        }

        $cancelResponseTransfer = $this->transaction->executeTransaction($cancelRequestTransfer);

        $this->updateOrderPayment(
            $cancelRequestTransfer,
            $cancelResponseTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelRequestTransfer
     */
    protected function buildCancelRequestForOrderItem(ItemTransfer $itemTransfer, AfterpayCallTransfer $afterpayCallTransfer)
    {
        $cancelRequestTransfer = $this->cancelRequestBuilder
            ->buildBaseCancelRequestForOrder($afterpayCallTransfer);

        $this->cancelRequestBuilder
            ->addOrderItemToCancelRequest(
                $itemTransfer,
                $cancelRequestTransfer
            );

        return $cancelRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayPaymentTransfer $paymentTransfer
     *
     * @return bool
     */
    protected function isExpenseShouldBeCancelled(
        AfterpayCancelRequestTransfer $cancelRequestTransfer,
        AfterpayPaymentTransfer $paymentTransfer
    ) {
        $amountToCancelDecimal = $cancelRequestTransfer->getCancellationDetails()->getTotalGrossAmount();
        $amountToCancelInt = $this->money->convertDecimalToInteger((float)$amountToCancelDecimal);

        $amountCancelled = $paymentTransfer->getCancelledTotal();
        $amountAuthorized = $paymentTransfer->getAuthorizedTotal();

        $expenseTotal = $paymentTransfer->getExpenseTotal();

        $refundedTotal = $paymentTransfer->getExpenseTotal();

        return $amountToCancelInt + $amountCancelled + $expenseTotal + $refundedTotal === $amountAuthorized;
    }

    /**
     * @param int $expenseTotal
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function addExpensesToCancelRequest(
        $expenseTotal,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $this->cancelRequestBuilder
            ->addOrderExpenseToCancelRequest(
                $expenseTotal,
                $cancelRequestTransfer
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
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelResponseTransfer $cancelResponseTransfer
     *
     * @return void
     */
    protected function updateOrderPayment(
        AfterpayCancelRequestTransfer $cancelRequestTransfer,
        AfterpayCancelResponseTransfer $cancelResponseTransfer
    ) {
        if (!$cancelResponseTransfer->getTotalAuthorizedAmount()) {
            return;
        }

        $amountToCancelDecimal = $cancelRequestTransfer->getCancellationDetails()->getTotalGrossAmount();
        $amountToCancelInt = $this->money->convertDecimalToInteger((float)$amountToCancelDecimal);

        $this->paymentWriter->increaseTotalCancelledAmountByIdSalesOrder(
            $amountToCancelInt,
            $cancelRequestTransfer->getIdSalesOrder()
        );
    }
}
