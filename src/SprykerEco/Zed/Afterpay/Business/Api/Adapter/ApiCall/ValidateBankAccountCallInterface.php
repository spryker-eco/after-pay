<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer;

interface ValidateBankAccountCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function execute(AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterpayValidateBankAccountResponseTransfer;
}
