<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \SprykerEco\Yves\AfterPay\AfterPayFactory getFactory()
 * @method \SprykerEco\Client\AfterPay\AfterPayClientInterface getClient()
 */
class AfterPayCustomerLookupPlugin extends AbstractPlugin implements AfterPayCustomerLookupPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer
    {
        return $this->getFactory()
            ->getClient()
            ->lookupCustomer($customerLookupRequestTransfer);
    }
}
