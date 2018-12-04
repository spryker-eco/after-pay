<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;

interface PrePaymentQuoteExpanderPluginInterface
{
    /**
     * Specification:
     *  - Expands quote with some information before the payment step is displayed. In case of
     * AfterPay 2+ step authorization, it is the "available payment methods" information.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuote(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
