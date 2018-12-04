<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Converter;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\OrderTransfer;

class OrderToCallConverter implements OrderToCallConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    public function convert(OrderTransfer $orderTransfer): AfterPayCallTransfer
    {
        $afterpayCallTransfer = new AfterPayCallTransfer();
        $afterpayCallTransfer->setOrderReference($orderTransfer->getOrderReference());
        $afterpayCallTransfer->setEmail($orderTransfer->getCustomer()->getEmail());
        $afterpayCallTransfer->setItems($orderTransfer->getItems());
        $afterpayCallTransfer->setBillingAddress($orderTransfer->getBillingAddress());
        $afterpayCallTransfer->setShippingAddress($orderTransfer->getShippingAddress());
        $afterpayCallTransfer->setTotals($orderTransfer->getTotals());
        $afterpayCallTransfer->setPaymentMethod($orderTransfer->getAfterPayPayment()->getPaymentMethod());

        return $afterpayCallTransfer;
    }
}
