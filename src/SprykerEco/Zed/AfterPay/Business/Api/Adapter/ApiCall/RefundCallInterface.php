<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRefundResponseTransfer;

interface RefundCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayRefundRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundResponseTransfer
     */
    public function execute(AfterPayRefundRequestTransfer $requestTransfer): AfterPayRefundResponseTransfer;
}
