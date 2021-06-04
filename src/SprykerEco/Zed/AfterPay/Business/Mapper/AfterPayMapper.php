<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Mapper;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class AfterPayMapper implements AfterPayMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    public function mapQuoteTransferToAfterPayCallTransfer(QuoteTransfer $quoteTransfer): AfterPayCallTransfer
    {
        $afterPayCallTransfer = $this->buildAfterPayCallTransfer($quoteTransfer);
        $this->addItemsTotalTaxToAfterPayCallTransfer($afterPayCallTransfer, $quoteTransfer);

        return $afterPayCallTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    protected function buildAfterPayCallTransfer(QuoteTransfer $quoteTransfer): AfterPayCallTransfer
    {
        return (new AfterPayCallTransfer())
            ->setOrderReference($quoteTransfer->getOrderReference())
            ->setIdSalesOrder($quoteTransfer->getPayment()->getIdSalesOrder())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setItems($quoteTransfer->getItems())
            ->setBillingAddress($quoteTransfer->getBillingAddress())
            ->setShippingAddress($quoteTransfer->getShippingAddress())
            ->setTotals($quoteTransfer->getTotals())
            ->setCurrency($quoteTransfer->getCurrency()->getCode())
            ->setPayments($quoteTransfer->getPayments())
            ->setPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    protected function addItemsTotalTaxToAfterPayCallTransfer(
        AfterPayCallTransfer $afterPayCallTransfer,
        QuoteTransfer $quoteTransfer
    ): AfterPayCallTransfer {
        $itemsTotalTaxAmount = 0;
        foreach ($quoteTransfer->getItems() as $item) {
            $itemsTotalTaxAmount += $item->getUnitTaxAmountFullAggregation();
        }
        $afterPayCallTransfer->getTotals()->getTaxTotal()->setItemsTotalTax($itemsTotalTaxAmount);

        return $afterPayCallTransfer;
    }
}
