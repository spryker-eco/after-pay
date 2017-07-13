<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\OrderTransfer;

interface PriceToPayProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return int
     */
    public function getPriceToPayForOrder(OrderTransfer $orderWithPaymentTransfer);
}
