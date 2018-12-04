<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund;

use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;

class RefundRequestBuilder implements RefundRequestBuilderInterface
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
     * @return \Generated\Shared\Transfer\AfterpayRefundRequestTransfer
     */
    public function buildBaseRefundRequestForOrder(OrderTransfer $orderTransfer): AfterpayRefundRequestTransfer
    {
        $refundRequestTransfer = $this->orderToRequestMapper
            ->orderToBaseRefundRequest($orderTransfer);

        return $refundRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    public function addOrderItemToRefundRequest(
        ItemTransfer $orderItemTransfer,
        AfterpayRefundRequestTransfer $refundRequestTransfer
    ): RefundRequestBuilderInterface {

        $orderItemRequestTransfer = $this->orderToRequestMapper->orderItemToAfterpayItemRequest($orderItemTransfer);

        $this->addOrderItemToRefundDetails($orderItemRequestTransfer, $refundRequestTransfer);

        return $this;
    }

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    public function addOrderExpenseToRefundRequest(
        int $expenseAmount,
        AfterpayRefundRequestTransfer $refundRequestTransfer
    ): RefundRequestBuilderInterface {
        $expenseItemRequestTransfer = $this->buildExpenseItemTransfer($expenseAmount);
        $this->addOrderItemToRefundRequest($expenseItemRequestTransfer, $refundRequestTransfer);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $refundRequestTransfer
     *
     * @return void
     */
    protected function addOrderItemToRefundDetails(
        AfterpayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterpayRefundRequestTransfer $refundRequestTransfer
    ): void {
        $refundRequestTransfer->addOrderItem($orderItemRequestTransfer);
    }

    /**
     * @param int $expenseAmount
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function buildExpenseItemTransfer(int $expenseAmount): ItemTransfer
    {
        return (new ItemTransfer())
            ->setSku(AfterpayConfig::REFUND_EXPENSE_SKU)
            ->setName(AfterpayConfig::REFUND_EXPENSE_DECRIPTION)
            ->setUnitGrossPrice($expenseAmount)
            ->setUnitPriceToPayAggregation($expenseAmount)
            ->setUnitTaxAmountFullAggregation(0)
            ->setQuantity(1);
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
