<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Converter;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\OrderTransfer;

class OrderToCallConverter implements OrderToCallConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCallTransfer
     */
    public function convert(OrderTransfer $orderTransfer)
    {
        $afterpayCallTransfer = new AfterpayCallTransfer();
        $afterpayCallTransfer->setOrderReference($orderTransfer->getOrderReference());
        $afterpayCallTransfer->setEmail($orderTransfer->getCustomer()->getEmail());
        $afterpayCallTransfer->setItems($orderTransfer->getItems());
        $afterpayCallTransfer->setBillingAddress($orderTransfer->getBillingAddress());
        $afterpayCallTransfer->setShippingAddress($orderTransfer->getShippingAddress());
        $afterpayCallTransfer->setTotals($orderTransfer->getTotals());
        $afterpayCallTransfer->setPaymentMethod($orderTransfer->getAfterpayPayment()->getPaymentMethod());

        return $afterpayCallTransfer;
    }
}
