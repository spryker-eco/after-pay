<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund;

use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;

class RefundRequestBuilder implements RefundRequestBuilderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    protected $orderToRequestMapper;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    private $money;

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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundRequestTransfer
     */
    public function buildBaseRefundRequestForOrder(OrderTransfer $orderTransfer): AfterPayRefundRequestTransfer
    {
        $refundRequestTransfer = $this->orderToRequestMapper
            ->orderToBaseRefundRequest($orderTransfer);

        return $refundRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    public function addOrderItemToRefundRequest(
        ItemTransfer $orderItemTransfer,
        AfterPayRefundRequestTransfer $refundRequestTransfer
    ): RefundRequestBuilderInterface {

        $orderItemRequestTransfer = $this->orderToRequestMapper->orderItemToAfterPayItemRequest($orderItemTransfer);

        $this->addOrderItemToRefundDetails($orderItemRequestTransfer, $refundRequestTransfer);

        return $this;
    }

    /**
     * @param int $expenseAmount
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    public function addOrderExpenseToRefundRequest(
        int $expenseAmount,
        AfterPayRefundRequestTransfer $refundRequestTransfer
    ): RefundRequestBuilderInterface {
        $expenseItemRequestTransfer = $this->buildExpenseItemTransfer($expenseAmount);
        $this->addOrderItemToRefundRequest($expenseItemRequestTransfer, $refundRequestTransfer);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer $orderItemRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $refundRequestTransfer
     *
     * @return void
     */
    protected function addOrderItemToRefundDetails(
        AfterPayRequestOrderItemTransfer $orderItemRequestTransfer,
        AfterPayRefundRequestTransfer $refundRequestTransfer
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
            ->setSku(AfterPayConfig::REFUND_EXPENSE_SKU)
            ->setName(AfterPayConfig::REFUND_EXPENSE_DECRIPTION)
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
