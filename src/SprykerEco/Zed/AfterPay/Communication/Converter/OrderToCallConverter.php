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
        $afterPayCallTransfer = new AfterPayCallTransfer();
        $afterPayCallTransfer->setOrderReference($orderTransfer->getOrderReference());
        $afterPayCallTransfer->setIdSalesOrder($orderTransfer->getIdSalesOrder());
        $afterPayCallTransfer->setEmail($orderTransfer->getCustomer()->getEmail());
        $afterPayCallTransfer->setItems($orderTransfer->getItems());
        $afterPayCallTransfer->setBillingAddress($orderTransfer->getBillingAddress());
        $afterPayCallTransfer->setShippingAddress($orderTransfer->getShippingAddress());
        $afterPayCallTransfer->setTotals($orderTransfer->getTotals());
        $afterPayCallTransfer->setPaymentMethod($orderTransfer->getAfterPayPayment()->getPaymentMethod());

        return $afterPayCallTransfer;
    }
}
