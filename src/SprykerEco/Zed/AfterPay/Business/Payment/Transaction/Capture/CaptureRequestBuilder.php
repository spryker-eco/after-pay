<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;

class CaptureRequestBuilder implements CaptureRequestBuilderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    protected $orderToRequestMapper;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    protected $money;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface $orderToRequestMapper
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $money
     */
    public function __construct(
        OrderToRequestTransferInterface $orderToRequestMapper,
        AfterPayToMoneyFacadeInterface $money
    ) {
        $this->orderToRequestMapper = $orderToRequestMapper;
        $this->money = $money;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    public function buildBaseCaptureRequestForOrder(AfterPayCallTransfer $afterPayCallTransfer): AfterPayCaptureRequestTransfer
    {
        $captureRequestTransfer = $this->orderToRequestMapper
            ->orderToBaseCaptureRequest($afterPayCallTransfer);

        return $captureRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToCaptureRequest(
        ItemTransfer $orderItemTransfer,
        AfterPayCaptureRequestTransfer $captureRequestTransfer
    ) {
        $orderItemRequestTransfer = $this->orderToRequestMapper->orderItemToAfterPayItemRequest($orderItemTransfer);

        $captureRequestTransfer->getOrderDetails()->addItem($orderItemRequestTransfer);
        $this->increaseTotalNetAmount($orderItemRequestTransfer, $captureRequestTransfer);
        $this->increaseTotalGrossAmount($orderItemRequestTransfer, $captureRequestTransfer);

        return $this;
    }

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToCaptureRequest(
        int $expenseAmount,
        AfterPayCaptureRequestTransfer $captureRequestTransfer
    ) {
        $expenseItemRequestTransfer = $this->buildExpenseItemTransfer($expenseAmount);
        $this->addOrderItemToCaptureRequest($expenseItemRequestTransfer, $captureRequestTransfer);

        return $this;
    }

    /**
     * @param int $expenseAmount
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function buildExpenseItemTransfer(int $expenseAmount): ItemTransfer
    {
        return (new ItemTransfer())
            ->setSku(AfterPayConfig::CAPTURE_EXPENSE_SKU)
            ->setName(AfterPayConfig::CAPTURE_EXPENSE_DESCRIPTION)
            ->setUnitGrossPrice($expenseAmount)
            ->setUnitPriceToPayAggregation($expenseAmount)
            ->setUnitTaxAmountFullAggregation(0)
            ->setQuantity(1);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalNetAmount(
        AfterPayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterPayCaptureRequestTransfer $captureRequestTransfer
    ): void {
        $oldNetAmountDecimal = $this->decimalToInt((float)$captureRequestTransfer->getOrderDetails()->getTotalNetAmount());
        $itemNetAmountDecimal = $this->decimalToInt((float)$orderItemRequestTransfer->getNetUnitPrice());

        $newNetAmountDecimal = $oldNetAmountDecimal + $itemNetAmountDecimal;
        $captureRequestTransfer->getOrderDetails()->setTotalNetAmount(
            $this->intToDecimalString($newNetAmountDecimal)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalGrossAmount(
        AfterPayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterPayCaptureRequestTransfer $captureRequestTransfer
    ): void {
        $oldGrossAmountDecimal = $this->decimalToInt((float)$captureRequestTransfer->getOrderDetails()->getTotalGrossAmount());
        $itemGrossAmountDecimal = $this->decimalToInt((float)$orderItemRequestTransfer->getGrossUnitPrice());

        $newGrossAmountDecimal = $oldGrossAmountDecimal + $itemGrossAmountDecimal;
        $captureRequestTransfer->getOrderDetails()->setTotalGrossAmount(
            $this->intToDecimalString($newGrossAmountDecimal)
        );
    }

    /**
     * @param float $decimalValue
     *
     * @return int
     */
    protected function decimalToInt(float $decimalValue): int
    {
        return $this->money->convertDecimalToInteger($decimalValue);
    }

    /**
     * @param int $intValue
     *
     * @return string
     */
    protected function intToDecimalString(int $intValue): string
    {
        return (string)$this->money->convertIntegerToDecimal($intValue);
    }
}
