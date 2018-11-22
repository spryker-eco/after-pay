<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder;

use Generated\Shared\Transfer\AfterpayCallTransfer;

interface AuthorizeRequestBuilderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    public function buildAuthorizeRequest(AfterpayCallTransfer $orderWithPaymentTransfer);
}
