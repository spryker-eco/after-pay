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
        /** @var \Generated\Shared\Transfer\PaymentTransfer $paymentTransfer */
        $paymentTransfer = $orderTransfer->getPayments()->offsetGet(0);

        return (new AfterPayCallTransfer())
            ->setOrderReference($orderTransfer->getOrderReference())
            ->setIdSalesOrder($orderTransfer->getIdSalesOrder())
            ->setEmail($orderTransfer->getCustomer()->getEmail())
            ->setItems($orderTransfer->getItems())
            ->setBillingAddress($orderTransfer->getBillingAddress())
            ->setShippingAddress($orderTransfer->getShippingAddress())
            ->setTotals($orderTransfer->getTotals())
            ->setPaymentMethod($paymentTransfer->getPaymentMethod())
            ->setExpenses($orderTransfer->getExpenses())
            ->setCheckoutId($this->getCheckoutId($orderTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string|null
     */
    protected function getCheckoutId(OrderTransfer $orderTransfer): ?string
    {
        if ($orderTransfer->getAfterPayPayment()) {
            return $orderTransfer->getAfterPayPayment()->getIdCheckout();
        }

        return null;
    }
}
