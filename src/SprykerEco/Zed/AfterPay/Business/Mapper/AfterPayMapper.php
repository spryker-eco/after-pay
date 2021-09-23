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
        return (new AfterPayCallTransfer())
            ->fromArray($quoteTransfer->toArray(), true)
            ->setOrderReference($quoteTransfer->getOrderReference())
            ->setIdSalesOrder($quoteTransfer->getPayment()->getIdSalesOrder())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection());
    }
}
