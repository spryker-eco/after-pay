<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;

interface AuthorizeTransactionInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    public function executeTransaction(AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer): AfterPayApiResponseTransfer;
}
