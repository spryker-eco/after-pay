<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\Plugin;

use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer;

interface AddressValidationPluginInterface
{
    /**
     * Specification:
     *  - Makes "validate-address" call to the afterpay API, in order to validate customer address.
     *  Response contains isValid flag along with correctedAddress.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterpayValidateCustomerResponseTransfer;
}
