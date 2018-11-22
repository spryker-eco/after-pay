<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Converter;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class QuoteToCallConverter implements QuoteToCallConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCallTransfer
     */
    public function convert(QuoteTransfer $quoteTransfer)
    {
        $afterpayCallTransfer = new AfterpayCallTransfer();
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
