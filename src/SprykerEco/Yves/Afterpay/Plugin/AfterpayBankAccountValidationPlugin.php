<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\Plugin;

use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \SprykerEco\Yves\Afterpay\AfterpayFactory getFactory()
 */
class AfterpayBankAccountValidationPlugin extends AbstractPlugin implements BankAccountValidationPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterpayValidateBankAccountResponseTransfer
    {
        return $this
            ->getFactory()
            ->getAfterpayClient()
            ->validateBankAccount($validateBankAccountRequestTransfer);
    }
}
