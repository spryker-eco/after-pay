<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Capture;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;

class CaptureRequestBuilder implements CaptureRequestBuilderInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    protected $orderToRequestMapper;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    private $money;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface $orderToRequestMapper
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $money
     */
    public function __construct(
        OrderToRequestTransferInterface $orderToRequestMapper,
        AfterpayToMoneyInterface $money
    ) {
        $this->orderToRequestMapper = $orderToRequestMapper;
        $this->money = $money;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer
     */
    public function buildBaseCaptureRequestForOrder(AfterpayCallTransfer $afterpayCallTransfer): AfterpayCaptureRequestTransfer
    {
        $captureRequestTransfer = $this->orderToRequestMapper
            ->orderToBaseCaptureRequest($afterpayCallTransfer);

        return $captureRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToCaptureRequest(
        ItemTransfer $orderItemTransfer,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    ) {
        $orderItemRequestTransfer = $this->orderToRequestMapper->orderItemToAfterpayItemRequest($orderItemTransfer);

        $this->addOrderItemToOrderDetails($orderItemRequestTransfer, $captureRequestTransfer);
        $this->increaseTotalToCaptureAmounts($orderItemRequestTransfer, $captureRequestTransfer);

        return $this;
    }

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToCaptureRequest(
        int $expenseAmount,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    ) {
        $expenseItemRequestTransfer = $this->buildExpenseItemTransfer($expenseAmount);
        $this->addOrderItemToCaptureRequest($expenseItemRequestTransfer, $captureRequestTransfer);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return void
     */
    protected function addOrderItemToOrderDetails(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    ): void {
        $captureRequestTransfer->getOrderDetails()->addItem($orderItemRequestTransfer->setGrossUnitPrice(1900));
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalToCaptureAmounts(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    ): void {
        $this->increaseTotalNetAmount($orderItemRequestTransfer, $captureRequestTransfer);
        $this->increaseTotalGrossAmount($orderItemRequestTransfer, $captureRequestTransfer);
    }

    /**
     * @param int $expenseAmount
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function buildExpenseItemTransfer(int $expenseAmount): ItemTransfer
    {
        return (new ItemTransfer())
            ->setSku(AfterpayConfig::CAPTURE_EXPENSE_SKU)
            ->setName(AfterpayConfig::CAPTURE_EXPENSE_DESCRIPTION)
            ->setUnitGrossPrice($expenseAmount)
            ->setUnitPriceToPayAggregation($expenseAmount)
            ->setUnitTaxAmountFullAggregation(0)
            ->setQuantity(1);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalNetAmount(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
    ): void {
        $oldNetAmountDecimal = $this->decimalToInt((float)$captureRequestTransfer->getOrderDetails()->getTotalNetAmount());
        $itemNetAmountDecimal = $this->decimalToInt((float)$orderItemRequestTransfer->getNetUnitPrice());

        $newNetAmountDecimal = $oldNetAmountDecimal + $itemNetAmountDecimal;
        $captureRequestTransfer->getOrderDetails()->setTotalNetAmount(
            $this->intToDecimalString($newNetAmountDecimal)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $captureRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalGrossAmount(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCaptureRequestTransfer $captureRequestTransfer
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
