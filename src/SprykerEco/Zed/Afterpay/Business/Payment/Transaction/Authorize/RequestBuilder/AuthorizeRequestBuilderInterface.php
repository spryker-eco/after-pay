<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder;

use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;

interface AuthorizeRequestBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    public function buildAuthorizeRequest(AfterpayCallTransfer $orderWithPaymentTransfer): AfterpayAuthorizeRequestTransfer;
}
