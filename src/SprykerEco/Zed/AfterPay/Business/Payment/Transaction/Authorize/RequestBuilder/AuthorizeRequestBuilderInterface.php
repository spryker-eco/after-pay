<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder;

use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;

interface AuthorizeRequestBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer
     */
    public function buildAuthorizeRequest(AfterPayCallTransfer $orderWithPaymentTransfer): AfterPayAuthorizeRequestTransfer;
}
