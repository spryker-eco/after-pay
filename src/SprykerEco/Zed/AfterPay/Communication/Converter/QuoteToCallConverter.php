<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Converter;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class QuoteToCallConverter implements QuoteToCallConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    public function convert(QuoteTransfer $quoteTransfer): AfterPayCallTransfer
    {
        return (new AfterPayCallTransfer())
            ->setOrderReference($quoteTransfer->getOrderReference())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setItems($quoteTransfer->getItems())
            ->setBillingAddress($quoteTransfer->getBillingAddress())
            ->setShippingAddress($quoteTransfer->getShippingAddress())
            ->setTotals($quoteTransfer->getTotals())
            ->setPayments($quoteTransfer->getPayments())
            ->setPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection());
    }
}
