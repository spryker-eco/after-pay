<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\OrderTransfer;

interface PriceToPayProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return int
     */
    public function getPriceToPayForOrder(OrderTransfer $orderWithPaymentTransfer): int;
}
