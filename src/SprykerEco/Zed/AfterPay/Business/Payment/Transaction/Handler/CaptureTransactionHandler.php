<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayCaptureResponseTransfer;
use Generated\Shared\Transfer\AfterPayPaymentTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CaptureTransactionInterface;

class CaptureTransactionHandler implements CaptureTransactionHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CaptureTransactionInterface
     */
    protected $transaction;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface
     */
    protected $paymentReader;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface
     */
    protected $captureRequestBuilder;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface
     */
    protected $paymentWriter;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CaptureTransactionInterface $transaction
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface $paymentReader
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface $paymentWriter
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface $captureRequestBuilder
     */
    public function __construct(
        CaptureTransactionInterface $transaction,
        PaymentReaderInterface $paymentReader,
        PaymentWriterInterface $paymentWriter,
        CaptureRequestBuilderInterface $captureRequestBuilder
    ) {
        $this->transaction = $transaction;
        $this->paymentReader = $paymentReader;
        $this->captureRequestBuilder = $captureRequestBuilder;
        $this->paymentWriter = $paymentWriter;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $items
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    public function capture(array $items, AfterPayCallTransfer $afterPayCallTransfer): void
    {
        $paymentTransfer = $this->getPaymentTransferForItem($afterPayCallTransfer);
        $captureRequestTransfer = $this->buildCaptureRequestForOrderItem($items, $afterPayCallTransfer);

        if ($paymentTransfer->getExpenseTotal()) {
            $this->processExpensesCapture($paymentTransfer, $afterPayCallTransfer);
        }

        $captureResponseTransfer = $this->transaction->executeTransaction($captureRequestTransfer);

        $this->updateOrderPayment($captureResponseTransfer, $afterPayCallTransfer->getIdSalesOrder());

        foreach ($items as $itemTransfer) {
            $this->updatePaymentOrderItem($captureResponseTransfer, $itemTransfer, $paymentTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    protected function processExpensesCapture(AfterPayPaymentTransfer $paymentTransfer, AfterPayCallTransfer $afterPayCallTransfer): void
    {
        if (!$this->isFirstItemToCapture($paymentTransfer)) {
            return;
        }
        $shipmentCaptureRequestTransfer = $this->buildExpensesCaptureRequest($paymentTransfer, $afterPayCallTransfer);
        $shipmentCaptureResponseTransfer = $this->transaction->executeTransaction($shipmentCaptureRequestTransfer);
        $this->updateOrderPayment($shipmentCaptureResponseTransfer, $afterPayCallTransfer->getIdSalesOrder());
        $this->updatePaymentWithExpensesCaptureNumber($shipmentCaptureResponseTransfer, $afterPayCallTransfer->getIdSalesOrder());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    protected function buildExpensesCaptureRequest(
        AfterPayPaymentTransfer $paymentTransfer,
        AfterPayCallTransfer $afterPayCallTransfer
    ): AfterPayCaptureRequestTransfer {
        $baseCaptureRequest = $this->captureRequestBuilder->buildBaseCaptureRequestForOrder($afterPayCallTransfer);
        $this->addExpensesToCaptureRequest($paymentTransfer->getExpenseTotal(), $baseCaptureRequest);

        return $baseCaptureRequest;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $items
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    protected function buildCaptureRequestForOrderItem(
        array $items,
        AfterPayCallTransfer $afterPayCallTransfer
    ): AfterPayCaptureRequestTransfer {
        $captureRequestTransfer = $this->captureRequestBuilder->buildBaseCaptureRequestForOrder($afterPayCallTransfer);

        foreach ($items as $itemTransfer) {
            $this->captureRequestBuilder->addOrderItemToCaptureRequest($itemTransfer, $captureRequestTransfer);
        }

        return $captureRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     *
     * @return bool
     */
    protected function isFirstItemToCapture(AfterPayPaymentTransfer $paymentTransfer): bool
    {
        return $paymentTransfer->getCapturedTotal() + $paymentTransfer->getRefundedTotal() === 0;
    }

    /**
     * @param int $expenseTotal
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return void
     */
    protected function addExpensesToCaptureRequest(
        int $expenseTotal,
        AfterPayCaptureRequestTransfer $captureRequestTransfer
    ): void {
        $this->captureRequestBuilder->addOrderExpenseToCaptureRequest($expenseTotal, $captureRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    protected function getPaymentTransferForItem(AfterPayCallTransfer $afterPayCallTransfer): AfterPayPaymentTransfer
    {
        return $this->paymentReader->getPaymentByIdSalesOrder($afterPayCallTransfer->getIdSalesOrder());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer $capturedResponseTransfer
     * @param int $idSalesOrder
     *
     * @return void
     */
    protected function updateOrderPayment(
        AfterPayCaptureResponseTransfer $capturedResponseTransfer,
        int $idSalesOrder
    ): void {
        if (!$capturedResponseTransfer->getCapturedAmount()) {
            return;
        }

        $this->paymentWriter->increaseTotalCapturedAmountByIdSalesOrder(
            $capturedResponseTransfer->getCapturedAmount(),
            $idSalesOrder,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer $capturedResponseTransfer
     * @param int $idSalesOrder
     *
     * @return void
     */
    protected function updatePaymentWithExpensesCaptureNumber(
        AfterPayCaptureResponseTransfer $capturedResponseTransfer,
        int $idSalesOrder
    ): void {
        if (!$capturedResponseTransfer->getCaptureNumber()) {
            return;
        }

        $this->paymentWriter->updateExpensesCaptureNumber(
            $capturedResponseTransfer->getCaptureNumber(),
            $idSalesOrder,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer $captureResponseTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     *
     * @return void
     */
    protected function updatePaymentOrderItem(
        AfterPayCaptureResponseTransfer $captureResponseTransfer,
        ItemTransfer $itemTransfer,
        AfterPayPaymentTransfer $paymentTransfer
    ): void {
        $this->paymentWriter->setCaptureNumberByIdSalesOrderItemAndIdPayment(
            $captureResponseTransfer->getCaptureNumber(),
            $itemTransfer->getIdSalesOrderItem(),
            $paymentTransfer->getIdPaymentAfterPay(),
        );
    }
}
