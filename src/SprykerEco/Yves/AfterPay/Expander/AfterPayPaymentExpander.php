<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\Expander;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use Symfony\Component\HttpFoundation\Request;

class AfterPayPaymentExpander implements AfterPayPaymentExpanderInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentToQuote(Request $request, QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();
        $quoteTransfer
            ->getPayment()
            ->setPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->setPaymentMethod($paymentSelection);

        return $quoteTransfer;
    }
}
