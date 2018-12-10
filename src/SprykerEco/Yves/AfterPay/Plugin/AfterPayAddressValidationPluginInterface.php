<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;

interface AfterPayAddressValidationPluginInterface
{
    /**
     * Specification:
     *  - Makes "validate-address" call to the AfterPay API, in order to validate customer address.
     *  Response contains isValid flag along with correctedAddress.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer;
}
