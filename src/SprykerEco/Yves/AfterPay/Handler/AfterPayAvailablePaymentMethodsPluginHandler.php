<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Handler;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Client\AfterPay\AfterPayClientInterface;

class AfterPayAvailablePaymentMethodsPluginHandler implements AfterPayAvailablePaymentMethodsPluginHandlerInterface
{
    /**
     * @var \SprykerEco\Client\AfterPay\AfterPayClientInterface
     */
    protected $afterPayClient;

    /**
     * @param \SprykerEco\Client\AfterPay\AfterPayClientInterface $afterPayClient
     */
    public function __construct(AfterPayClientInterface $afterPayClient)
    {
        $this->afterPayClient = $afterPayClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addAvailablePaymentMethodsToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getAfterPayAvailablePaymentMethods() !== null) {
            return $quoteTransfer;
        }

        $availablePaymentMethods = $this->afterPayClient->getAvailablePaymentMethods($quoteTransfer);

        return $quoteTransfer
            ->setAfterPayAvailablePaymentMethods($availablePaymentMethods);
    }
}
