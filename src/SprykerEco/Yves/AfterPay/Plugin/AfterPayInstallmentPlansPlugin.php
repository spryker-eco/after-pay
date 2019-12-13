<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \SprykerEco\Yves\AfterPay\AfterPayFactory getFactory()
 * @method \SprykerEco\Client\AfterPay\AfterPayClientInterface getClient()
 */
class AfterPayInstallmentPlansPlugin extends AbstractPlugin implements AfterPayInstallmentPlansPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterPayInstallmentPlansResponseTransfer
    {
        return $this->getFactory()
            ->getClient()
            ->getAvailableInstallmentPlans($installmentPlansRequestTransfer);
    }
}
