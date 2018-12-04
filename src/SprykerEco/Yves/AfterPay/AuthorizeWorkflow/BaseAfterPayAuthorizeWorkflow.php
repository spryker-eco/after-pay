<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\AuthorizeWorkflow;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;

class BaseAfterPayAuthorizeWorkflow
{
    public const PAYMENT_PROVIDER = AfterPayConfig::PROVIDER_NAME;
    public const PAYMENT_METHODS = [
        AfterPayConfig::PAYMENT_METHOD_INVOICE => AfterPayConfig::PAYMENT_METHOD_INVOICE,
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
