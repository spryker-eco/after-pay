<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \SprykerEco\Yves\AfterPay\AfterPayFactory getFactory()
 * @method \SprykerEco\Client\AfterPay\AfterPayClientInterface getClient()
 */
class AfterPayBankAccountValidationPlugin extends AbstractPlugin implements AfterPayBankAccountValidationPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterPayValidateBankAccountResponseTransfer
    {
        return $this->getFactory()
            ->getClient()
            ->validateBankAccount($validateBankAccountRequestTransfer);
    }
}
