<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Converter;

use Generated\Shared\Transfer\OrderTransfer;

interface OrderToCallConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCallTransfer
     */
    public function convert(OrderTransfer $orderTransfer);
}
