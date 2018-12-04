<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

class BaseAfterpayAuthorizeWorkflow
{
    public const PAYMENT_PROVIDER = AfterpayConfig::PROVIDER_NAME;
    public const PAYMENT_METHODS = [
        AfterpayConfig::PAYMENT_METHOD_INVOICE => AfterpayConfig::PAYMENT_METHOD_INVOICE,
    ];

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentDataToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();

        $quoteTransfer->getPayment()
            ->setPaymentProvider(static::PAYMENT_PROVIDER)
            ->setPaymentMethod(static::PAYMENT_METHODS[$paymentSelection]);

        return $quoteTransfer;
    }
}
