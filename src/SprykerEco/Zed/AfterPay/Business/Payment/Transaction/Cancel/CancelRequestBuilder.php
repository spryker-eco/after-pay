<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Cancel;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;

class CancelRequestBuilder implements CancelRequestBuilderInterface
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
     * @return \Generated\Shared\Transfer\AfterPayCancelRequestTransfer
     */
    public function buildBaseCancelRequestForOrder(AfterPayCallTransfer $afterPayCallTransfer): AfterPayCancelRequestTransfer
    {
        $cancelRequestTransfer = $this->orderToRequestMapper
            ->orderToBaseCancelRequest($afterPayCallTransfer);

        return $cancelRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToCancelRequest(
        ItemTransfer $orderItemTransfer,
        AfterPayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $orderItemRequestTransfer = $this->orderToRequestMapper->orderItemToAfterPayItemRequest($orderItemTransfer);

        $this->addOrderItemToOrderDetails($orderItemRequestTransfer, $cancelRequestTransfer);
        $this->increaseTotalToCancelAmounts($orderItemRequestTransfer, $cancelRequestTransfer);

        return $this;
    }

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToCancelRequest(
        int $expenseAmount,
        AfterPayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $expenseItemRequestTransfer = $this->buildExpenseItemTransfer($expenseAmount);
        $this->addOrderItemToCancelRequest($expenseItemRequestTransfer, $cancelRequestTransfer);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function addOrderItemToOrderDetails(
        AfterPayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterPayCancelRequestTransfer $cancelRequestTransfer
    ): void {
        $cancelRequestTransfer->getCancellationDetails()->addItem($orderItemRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalToCancelAmounts(
        AfterPayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterPayCancelRequestTransfer $cancelRequestTransfer
    ): void {
        $this->increaseTotalNetAmount($orderItemRequestTransfer, $cancelRequestTransfer);
        $this->increaseTotalGrossAmount($orderItemRequestTransfer, $cancelRequestTransfer);
    }

    /**
     * @param int $expenseAmount
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function buildExpenseItemTransfer(int $expenseAmount): ItemTransfer
    {
        return (new ItemTransfer())
            ->setSku(AfterPayConfig::CANCEL_EXPENSE_SKU)
            ->setName(AfterPayConfig::CANCEL_EXPENSE_DESCRIPTION)
            ->setUnitGrossPrice($expenseAmount)
            ->setUnitPriceToPayAggregation($expenseAmount)
            ->setUnitTaxAmountFullAggregation(0)
            ->setQuantity(1);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalNetAmount(
        AfterPayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterPayCancelRequestTransfer $cancelRequestTransfer
    ): void {
        $oldNetAmountDecimal = $this->decimalToInt((float)$cancelRequestTransfer->getCancellationDetails()->getTotalNetAmount());
        $itemNetAmountDecimal = $this->decimalToInt((float)$orderItemRequestTransfer->getNetUnitPrice());

        $newNetAmountDecimal = $oldNetAmountDecimal + $itemNetAmountDecimal;
        $cancelRequestTransfer->getCancellationDetails()->setTotalNetAmount(
            $this->intToDecimalString($newNetAmountDecimal)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalGrossAmount(
        AfterPayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterPayCancelRequestTransfer $cancelRequestTransfer
    ): void {
        $oldGrossAmountDecimal = $this->decimalToInt((float)$cancelRequestTransfer->getCancellationDetails()->getTotalGrossAmount());
        $itemGrossAmountDecimal = $this->decimalToInt((float)$orderItemRequestTransfer->getGrossUnitPrice());

        $newGrossAmountDecimal = $oldGrossAmountDecimal + $itemGrossAmountDecimal;
        $cancelRequestTransfer->getCancellationDetails()->setTotalGrossAmount(
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
