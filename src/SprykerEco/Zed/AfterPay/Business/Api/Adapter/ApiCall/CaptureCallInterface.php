<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayCaptureResponseTransfer;

interface CaptureCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer
     */
    public function execute(AfterPayCaptureRequestTransfer $requestTransfer): AfterPayCaptureResponseTransfer;
}
