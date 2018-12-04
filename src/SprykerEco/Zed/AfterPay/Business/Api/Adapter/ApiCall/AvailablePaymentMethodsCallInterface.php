<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer;

interface AvailablePaymentMethodsCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer
     */
    public function execute(AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer): AfterPayAvailablePaymentMethodsResponseTransfer;
}
