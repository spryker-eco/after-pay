<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;

interface RefundCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    public function execute(AfterpayRefundRequestTransfer $requestTransfer);
}
