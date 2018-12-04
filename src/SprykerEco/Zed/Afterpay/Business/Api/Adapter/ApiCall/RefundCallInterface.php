<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundResponseTransfer;

interface RefundCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    public function execute(AfterpayRefundRequestTransfer $requestTransfer): AfterpayRefundResponseTransfer;
}
