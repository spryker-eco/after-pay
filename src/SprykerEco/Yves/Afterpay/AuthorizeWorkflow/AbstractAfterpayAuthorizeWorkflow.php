<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

class AbstractAfterpayAuthorizeWorkflow
{
    const PAYMENT_PROVIDER = AfterpayConstants::PROVIDER_NAME;

    /**
     * @var array
     */
    protected static $paymentMethods = [
        AfterpayConstants::PAYMENT_METHOD_INVOICE => AfterpayConstants::PAYMENT_METHOD_INVOICE,
    ];

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentDataToQuote(QuoteTransfer $quoteTransfer)
    {
        $paymentSelection = $quoteTransfer->getPayment()->getPaymentSelection();

        $quoteTransfer->getPayment()
            ->setPaymentProvider(static::PAYMENT_PROVIDER)
            ->setPaymentMethod(static::$paymentMethods[$paymentSelection]);

        return $quoteTransfer;
    }
}
