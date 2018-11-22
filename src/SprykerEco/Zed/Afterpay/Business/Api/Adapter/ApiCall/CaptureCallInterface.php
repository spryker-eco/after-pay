<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;

interface CaptureCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    public function execute(AfterpayCaptureRequestTransfer $requestTransfer);
}
