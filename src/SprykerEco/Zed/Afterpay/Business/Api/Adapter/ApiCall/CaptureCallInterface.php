<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureResponseTransfer;

interface CaptureCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    public function execute(AfterpayCaptureRequestTransfer $requestTransfer): AfterpayCaptureResponseTransfer;
}
