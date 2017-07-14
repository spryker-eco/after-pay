<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Afterpay\Plugin;

use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;

interface InstallmentPlansPluginInterface
{

    /**
     * Specification:
     *  - Makes "validate bank-account" call to the afterpay API, to validate and evaluates the account and bank details
     *  in the context of direct debit payment. It is possible to transfer either the combination of BankCode and AccountNumber or IBAN and BIC
     *  Response contains validation result and list of risk-check messages
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(
        AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    );

}
