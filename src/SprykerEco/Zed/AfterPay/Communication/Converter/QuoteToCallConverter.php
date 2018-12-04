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
        $afterpayCallTransfer = new AfterPayCallTransfer();
        $afterpayCallTransfer->setOrderReference($quoteTransfer->getOrderReference());
        $afterpayCallTransfer->setEmail($quoteTransfer->getCustomer()->getEmail());
        $afterpayCallTransfer->setItems($quoteTransfer->getItems());
        $afterpayCallTransfer->setBillingAddress($quoteTransfer->getBillingAddress());
        $afterpayCallTransfer->setShippingAddress($quoteTransfer->getShippingAddress());
        $afterpayCallTransfer->setTotals($quoteTransfer->getTotals());
        $afterpayCallTransfer->setPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection());

        return $afterpayCallTransfer;
    }
}
