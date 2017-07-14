<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Client\Afterpay\AfterpayClientInterface;

class AvailablePaymentMethodsStep implements AvailablePaymentMethodsStepInterface
{

    /**
     * @var \SprykerEco\Client\Afterpay\AfterpayClientInterface
     */
    protected $afterpayClient;

    /**
     * @param \SprykerEco\Client\Afterpay\AfterpayClientInterface $afterpayClient
     */
    public function __construct(AfterpayClientInterface $afterpayClient)
    {
        $this->afterpayClient = $afterpayClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer)
    {
        $this->setAvailablePaymentMethodsToQuote($quoteTransfer);

        return $quoteTransfer->getAfterpayAvailablePaymentMethods();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function setAvailablePaymentMethodsToQuote(QuoteTransfer $quoteTransfer)
    {
        $availablePaymentMethods = $this->afterpayClient->getAvailablePaymentMethods($quoteTransfer);
        $quoteTransfer->setAfterpayAvailablePaymentMethods($availablePaymentMethods);
    }

}
