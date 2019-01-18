<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \SprykerEco\Yves\AfterPay\AfterPayFactory getFactory()
 * @method \SprykerEco\Client\AfterPay\AfterPayClientInterface getClient()
 */
class AfterPayAddAvailablePaymentMethodsToQuotePlugin extends AbstractPlugin implements AfterPayAddAvailablePaymentMethodsToQuotePluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addAvailablePaymentMethodsToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this
            ->getFactory()
            ->createAfterPayAvailablePaymentMethodsPluginHandler()
            ->addAvailablePaymentMethodsToQuote($quoteTransfer);
    }
}
