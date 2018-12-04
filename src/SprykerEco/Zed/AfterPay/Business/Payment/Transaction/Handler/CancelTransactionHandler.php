<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCancelResponseTransfer;
use Generated\Shared\Transfer\AfterPayPaymentTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CancelTransactionInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;

class CancelTransactionHandler implements CancelTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CancelTransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface
     */
    protected $paymentReader;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface
     */
    private $cancelRequestBuilder;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface
     */
    private $paymentWriter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    private $money;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CancelTransactionInterface $transaction
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface $paymentReader
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface $paymentWriter
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $money
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface $cancelRequestBuilder
     */
    public function __construct(
        CancelTransactionInterface $transaction,
        PaymentReaderInterface $paymentReader,
        PaymentWriterInterface $paymentWriter,
        AfterPayToMoneyFacadeInterface $money,
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
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterpayCallTransfer
     *
     * @return void
     */
    public function cancel(ItemTransfer $itemTransfer, AfterPayCallTransfer $afterpayCallTransfer): void
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
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelRequestTransfer
     */
    protected function buildCancelRequestForOrderItem(
        ItemTransfer $itemTransfer,
        AfterPayCallTransfer $afterpayCallTransfer
    ): AfterPayCancelRequestTransfer {
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
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     *
     * @return bool
     */
    protected function isExpenseShouldBeCancelled(
        AfterPayCancelRequestTransfer $cancelRequestTransfer,
        AfterPayPaymentTransfer $paymentTransfer
    ): bool {
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
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function addExpensesToCancelRequest(
        $expenseTotal,
        AfterPayCancelRequestTransfer $cancelRequestTransfer
    ): void {
        $this->cancelRequestBuilder
            ->addOrderExpenseToCancelRequest(
                $expenseTotal,
                $cancelRequestTransfer
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    protected function getPaymentTransferForItem(ItemTransfer $itemTransfer): AfterPayPaymentTransfer
    {
        return $this->paymentReader->getPaymentByIdSalesOrder($itemTransfer->getFkSalesOrder());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCancelResponseTransfer $cancelResponseTransfer
     *
     * @return void
     */
    protected function updateOrderPayment(
        AfterPayCancelRequestTransfer $cancelRequestTransfer,
        AfterPayCancelResponseTransfer $cancelResponseTransfer
    ): void {
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
