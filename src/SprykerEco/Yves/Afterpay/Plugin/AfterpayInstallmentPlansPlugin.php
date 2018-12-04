<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\Plugin;

use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \SprykerEco\Yves\Afterpay\AfterpayFactory getFactory()
 */
class AfterpayInstallmentPlansPlugin extends AbstractPlugin implements InstallmentPlansPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterpayInstallmentPlansResponseTransfer
    {
        return $this
            ->getFactory()
            ->getAfterpayClient()
            ->getAvailableInstallmentPlans($installmentPlansRequestTransfer);
    }
}
