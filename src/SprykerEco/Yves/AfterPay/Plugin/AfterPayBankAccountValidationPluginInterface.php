<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;

interface AfterPayBankAccountValidationPluginInterface
{
    /**
     * Specification:
     *  - Makes "validate bank-account" call to the AfterPay API, to validate and evaluates the account and bank details
     *  in the context of direct debit payment.
     *  - It is possible to transfer either the combination of BankCode and AccountNumber or IBAN and BIC.
     *  - Response contains validation result and list of risk-check messages.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(
        AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
    ): AfterPayValidateBankAccountResponseTransfer;
}
