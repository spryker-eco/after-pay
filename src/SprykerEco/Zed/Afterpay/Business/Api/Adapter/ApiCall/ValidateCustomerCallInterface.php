<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer;

interface ValidateCustomerCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function execute(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterpayValidateCustomerResponseTransfer;
}
