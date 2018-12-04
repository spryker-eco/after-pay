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
        $afterPayCallTransfer = new AfterPayCallTransfer();
        $afterPayCallTransfer->setOrderReference($quoteTransfer->getOrderReference());
        $afterPayCallTransfer->setEmail($quoteTransfer->getCustomer()->getEmail());
        $afterPayCallTransfer->setItems($quoteTransfer->getItems());
        $afterPayCallTransfer->setBillingAddress($quoteTransfer->getBillingAddress());
        $afterPayCallTransfer->setShippingAddress($quoteTransfer->getShippingAddress());
        $afterPayCallTransfer->setTotals($quoteTransfer->getTotals());
        $afterPayCallTransfer->setPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection());

        return $afterPayCallTransfer;
    }
}
