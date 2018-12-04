<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;

interface AuthorizePaymentCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function execute(AfterpayAuthorizeRequestTransfer $requestTransfer): AfterpayApiResponseTransfer;
}
