<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer;
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
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsTransfer
    {
        $this->setAvailablePaymentMethodsToQuote($quoteTransfer);

        return $quoteTransfer->getAfterpayAvailablePaymentMethods();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function setAvailablePaymentMethodsToQuote(QuoteTransfer $quoteTransfer): void
    {
        $availablePaymentMethods = $this->afterpayClient->getAvailablePaymentMethods($quoteTransfer);
        $quoteTransfer->setAfterpayAvailablePaymentMethods($availablePaymentMethods);
    }
}
