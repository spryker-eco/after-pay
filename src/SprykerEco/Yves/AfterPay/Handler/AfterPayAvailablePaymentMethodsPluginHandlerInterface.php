<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Handler;

use Generated\Shared\Transfer\QuoteTransfer;

interface AfterPayAvailablePaymentMethodsPluginHandlerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addAvailablePaymentMethodsToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
