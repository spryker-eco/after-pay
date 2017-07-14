<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;

interface CancelCallInterface
{

    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    public function execute(AfterpayCancelRequestTransfer $requestTransfer);

}
