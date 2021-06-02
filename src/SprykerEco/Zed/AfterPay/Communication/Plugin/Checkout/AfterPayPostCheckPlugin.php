<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutPostSaveHookInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\AfterPay\Business\AfterPayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface getQueryContainer()
 */
class AfterPayPostCheckPlugin extends AbstractPlugin implements CheckoutPostSaveHookInterface
{
    /**
     * {@inheritDoc}
     * - This plugin is called after the order is placed.
     * - Set the success flag to false, if redirect should be headed to an error page afterwords.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function executeHook(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse): void
    {
        $this->getFacade()->postSaveHook($quoteTransfer, $checkoutResponse);
    }
}
