<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;

interface CustomerLookupPluginInterface
{
    /**
     * Specification:
     *  - Makes "customer-lookup" call to the AfterPay API, to find customer based on social security number or mobile number.
     *  Response contains customer's account with list of addresses
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer;
}
