<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCancelResponseTransfer;

interface CancelCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelResponseTransfer
     */
    public function execute(AfterPayCancelRequestTransfer $requestTransfer): AfterPayCancelResponseTransfer;
}
