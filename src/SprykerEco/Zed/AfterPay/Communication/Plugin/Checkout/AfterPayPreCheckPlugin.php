<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreSaveHookInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @deprecated Use {@link \SprykerEco\Zed\AfterPay\Communication\Plugin\Checkout\AfterPayCheckoutPostSavePlugin} instead.
 *
 * @method \SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AfterPay\Business\AfterPayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface getQueryContainer()
 */
class AfterPayPreCheckPlugin extends AbstractPlugin implements CheckoutPreSaveHookInterface
{
    /**
     * {@inheritDoc}
     * - Proceed with Authorize Payment process.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function preSave(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): QuoteTransfer
    {
        $afterPayCallTransfer = $this->getFactory()
            ->createQuoteToCallConverter()
            ->convert($quoteTransfer);
        $this->getFacade()->authorizePayment($afterPayCallTransfer);

        return $quoteTransfer;
    }
}
