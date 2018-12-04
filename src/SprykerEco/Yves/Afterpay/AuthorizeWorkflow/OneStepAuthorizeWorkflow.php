<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow;

use Generated\Shared\Transfer\QuoteTransfer;

class OneStepAuthorizeWorkflow extends BaseAfterpayAuthorizeWorkflow implements AfterpayAuthorizeWorkflowInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteBeforePaymentStep(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $quoteTransfer;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentSubForms
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[]
     */
    public function filterAvailablePaymentMethods(array $paymentSubForms): array
    {
        return $paymentSubForms;
    }
}
