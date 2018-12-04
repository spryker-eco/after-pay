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
use Generated\Shared\Transfer\OrderTransfer;
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
    private $captureRequestBuilder;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface
     */
    private $paymentWriter;

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
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return void
     */
    public function capture(ItemTransfer $itemTransfer, AfterPayCallTransfer $afterPayCallTransfer): void
    {
        $captureRequestTransfer = $this->buildCaptureRequestForOrderItem($itemTransfer, $afterPayCallTransfer);
        $paymentTransfer = $this->getPaymentTransferForItem($itemTransfer);

        $this->processExpensesCapture($paymentTransfer, $orderTransfer);
        $captureResponseTransfer = $this->transaction->executeTransaction($captureRequestTransfer);

        $this->updateOrderPayment($captureResponseTransfer, $orderTransfer->getIdSalesOrder());
        $this->updatePaymentOrderItem(
            $captureResponseTransfer,
            $itemTransfer,
            $paymentTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    protected function processExpensesCapture(AfterPayPaymentTransfer $paymentTransfer, OrderTransfer $orderTransfer): void
    {
        if (!$this->isFirstItemToCapture($paymentTransfer)) {
            return;
        }
        $shipmentCaptureRequestTransfer = $this->buildExpensesCaptureRequest($paymentTransfer, $orderTransfer);
        $shipmentCaptureResponseTransfer = $this->transaction->executeTransaction($shipmentCaptureRequestTransfer);
        $this->updateOrderPayment($shipmentCaptureResponseTransfer, $orderTransfer->getIdSalesOrder());
        $this->updatePaymentWithExpensesCaptureNumber($shipmentCaptureResponseTransfer, $orderTransfer->getIdSalesOrder());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $paymentTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    protected function buildExpensesCaptureRequest(
        AfterPayPaymentTransfer $paymentTransfer,
        OrderTransfer $orderTransfer
    ): AfterPayCaptureRequestTransfer {
        $baseCaptureRequest = $this->captureRequestBuilder->buildBaseCaptureRequestForOrder($orderTransfer);
        $this->addExpensesToCaptureRequest($paymentTransfer->getExpenseTotal(), $baseCaptureRequest);

        return $baseCaptureRequest;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    protected function buildCaptureRequestForOrderItem(
        ItemTransfer $itemTransfer,
        AfterPayCallTransfer $afterPayCallTransfer
    ): AfterPayCaptureRequestTransfer {
        $captureRequestTransfer = $this->captureRequestBuilder
            ->buildBaseCaptureRequestForOrder($afterPayCallTransfer);

        $this->captureRequestBuilder
            ->addOrderItemToCaptureRequest(
                $itemTransfer,
                $captureRequestTransfer
            );

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
        $this->captureRequestBuilder
            ->addOrderExpenseToCaptureRequest(
                $expenseTotal,
                $captureRequestTransfer
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
            $idSalesOrder
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
            $idSalesOrder
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
            $paymentTransfer->getIdPaymentAfterPay()
        );
    }
}
