<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel;

use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;

class CancelRequestBuilder implements CancelRequestBuilderInterface
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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelRequestTransfer
     */
    public function buildBaseCancelRequestForOrder(OrderTransfer $orderTransfer)
    {
        $cancelRequestTransfer = $this->orderToRequestMapper
            ->orderToBaseCancelRequest($orderTransfer);

        return $cancelRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return $this
     */
    public function addOrderItemToCancelRequest(
        ItemTransfer $orderItemTransfer,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $orderItemRequestTransfer = $this->orderToRequestMapper->orderItemToAfterpayItemRequest($orderItemTransfer);

        $this->addOrderItemToOrderDetails($orderItemRequestTransfer, $cancelRequestTransfer);
        $this->increaseTotalToCancelAmounts($orderItemRequestTransfer, $cancelRequestTransfer);

        return $this;
    }

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return $this
     */
    public function addOrderExpenseToCancelRequest(
        $expenseAmount,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $expenseItemRequestTransfer = $this->buildExpenseItemTransfer($expenseAmount);
        $this->addOrderItemToCancelRequest($expenseItemRequestTransfer, $cancelRequestTransfer);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function addOrderItemToOrderDetails(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $cancelRequestTransfer->getCancellationDetails()->addItem($orderItemRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalToCancelAmounts(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $this->increaseTotalNetAmount($orderItemRequestTransfer, $cancelRequestTransfer);
        $this->increaseTotalGrossAmount($orderItemRequestTransfer, $cancelRequestTransfer);
    }

    /**
     * @param int $expenseAmount
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function buildExpenseItemTransfer($expenseAmount)
    {
        return (new ItemTransfer())
            ->setSku(AfterpayConstants::CANCEL_EXPENSE_SKU)
            ->setName(AfterpayConstants::CANCEL_EXPENSE_DESCRIPTION)
            ->setUnitGrossPrice($expenseAmount)
            ->setUnitPriceToPayAggregation($expenseAmount)
            ->setUnitTaxAmountFullAggregation(0)
            ->setQuantity(1);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalNetAmount(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $oldNetAmountDecimal = $this->decimalToInt((float)$cancelRequestTransfer->getCancellationDetails()->getTotalNetAmount());
        $itemNetAmountDecimal = $this->decimalToInt((float)$orderItemRequestTransfer->getNetUnitPrice());

        $newNetAmountDecimal = $oldNetAmountDecimal + $itemNetAmountDecimal;
        $cancelRequestTransfer->getCancellationDetails()->setTotalNetAmount(
            $this->intToDecimalString($newNetAmountDecimal)
        );
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $cancelRequestTransfer
     *
     * @return void
     */
    protected function increaseTotalGrossAmount(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayCancelRequestTransfer $cancelRequestTransfer
    ) {
        $oldGrossAmountDecimal = $this->decimalToInt((float)$cancelRequestTransfer->getCancellationDetails()->getTotalGrossAmount());
        $itemGrossAmountDecimal = $this->decimalToInt((float)$orderItemRequestTransfer->getGrossUnitPrice());

        $newGrossAmountDecimal = $oldGrossAmountDecimal + $itemGrossAmountDecimal;
        $cancelRequestTransfer->getCancellationDetails()->setTotalGrossAmount(
            $this->intToDecimalString($newGrossAmountDecimal)
        );
    }

    /**
     * @param string $decimalValue
     *
     * @return int
     */
    protected function decimalToInt($decimalValue)
    {
        return $this->money->convertDecimalToInteger($decimalValue);
    }

    /**
     * @param int $intValue
     *
     * @return string
     */
    protected function intToDecimalString($intValue)
    {
        return (string)$this->money->convertIntegerToDecimal($intValue);
    }

}
