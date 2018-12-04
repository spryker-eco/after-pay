<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Handler\RiskCheck;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface AvailablePaymentMethodsHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsTransfer;
}
