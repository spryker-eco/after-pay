<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\Plugin;

use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \SprykerEco\Yves\Afterpay\AfterpayFactory getFactory()
 */
class AfterpayCustomerLookupPlugin extends AbstractPlugin implements CustomerLookupPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterpayCustomerLookupResponseTransfer
    {
        return $this
            ->getFactory()
            ->getAfterpayClient()
            ->lookupCustomer($customerLookupRequestTransfer);
    }
}
