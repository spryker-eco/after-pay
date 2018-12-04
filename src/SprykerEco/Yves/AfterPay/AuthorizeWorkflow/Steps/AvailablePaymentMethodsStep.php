<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps;

use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Client\AfterPay\AfterPayClientInterface;

class AvailablePaymentMethodsStep implements AvailablePaymentMethodsStepInterface
{
    /**
     * @var \SprykerEco\Client\AfterPay\AfterPayClientInterface
     */
    protected $afterpayClient;

    /**
     * @param \SprykerEco\Client\AfterPay\AfterPayClientInterface $afterpayClient
     */
    public function __construct(AfterPayClientInterface $afterpayClient)
    {
        $this->afterpayClient = $afterpayClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsTransfer
    {
        $this->setAvailablePaymentMethodsToQuote($quoteTransfer);

        return $quoteTransfer->getAfterPayAvailablePaymentMethods();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function setAvailablePaymentMethodsToQuote(QuoteTransfer $quoteTransfer): void
    {
        $availablePaymentMethods = $this->afterpayClient->getAvailablePaymentMethods($quoteTransfer);
        $quoteTransfer->setAfterPayAvailablePaymentMethods($availablePaymentMethods);
    }
}
