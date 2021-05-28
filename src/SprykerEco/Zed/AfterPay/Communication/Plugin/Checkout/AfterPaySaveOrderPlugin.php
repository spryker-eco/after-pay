<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Plugin\Checkout;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\CheckoutExtension\Dependency\Plugin\CheckoutDoSaveOrderInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use SprykerEco\Shared\AfterPay\AfterPayConfig as SharedAfterPayConfig;

/**
 * @method \SprykerEco\Zed\AfterPay\Business\AfterPayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 * @method \SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface getQueryContainer()
 */
class AfterPaySaveOrderPlugin extends AbstractPlugin implements CheckoutDoSaveOrderInterface
{
    /**
     * {@inheritDoc}
     * - Retrieves (its) data from the quote object and saves it to the database.
     * - Proceed with Authorize Payment process.
     * - These plugins are already enveloped into a transaction.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrder(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer): void
    {
        if ($quoteTransfer->getPayment()->getPaymentProvider() !== SharedAfterPayConfig::PROVIDER_NAME) {
            return;
        }

        $afterPayFacade = $this->getFacade();
        $afterPayFacade->saveOrderPayment($quoteTransfer, $saveOrderTransfer);

        $afterPayCallTransfer = $this->getFactory()
            ->createQuoteToCallConverter()
            ->convert($quoteTransfer);

        $afterPayFacade->authorizePayment($afterPayCallTransfer);
    }
}
