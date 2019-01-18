<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;

interface AfterPayAddAvailablePaymentMethodsToQuotePluginInterface
{
    /**
     * Specification:
     *  - Makes "authorize" call to the AfterPay API, to get available payment methods.
     *  - Add AfterPay available payment methods to quote.
     *  - Fill AfterPayAvailablePaymentMethodsTransfer in quote if it was empty.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addAvailablePaymentMethodsToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
