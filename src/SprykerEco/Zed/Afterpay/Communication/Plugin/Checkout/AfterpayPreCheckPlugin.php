<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPreSaveHookInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\Afterpay\Communication\AfterpayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Afterpay\Business\AfterpayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface getQueryContainer()
 */
class AfterpayPreCheckPlugin extends AbstractPlugin implements CheckoutPreSaveHookInterface
{
    /**
     * Specification:
     * - Do something before orderTransfer save
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
        $afterpayCallTransfer = $this->getFactory()
            ->createQuoteToCallConverter()
            ->convert($quoteTransfer);
        $this->getFacade()->authorizePayment($afterpayCallTransfer);

        return $quoteTransfer;
    }
}
