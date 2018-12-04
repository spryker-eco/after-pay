<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\Plugin;

use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;

interface CustomerLookupPluginInterface
{
    /**
     * Specification:
     *  - Makes "customer-lookup" call to the afterpay API, to find customer based on social security number or mobile number.
     *  Response contains customer's account with list of addresses
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterpayCustomerLookupResponseTransfer;
}
