<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;

interface AuthorizePaymentCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    public function execute(AfterPayAuthorizeRequestTransfer $requestTransfer): AfterPayApiResponseTransfer;
}
