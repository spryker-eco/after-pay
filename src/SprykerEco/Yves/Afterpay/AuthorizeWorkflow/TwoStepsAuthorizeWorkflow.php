<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow;

use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStepInterface;

class TwoStepsAuthorizeWorkflow extends BaseAfterpayAuthorizeWorkflow implements AfterpayAuthorizeWorkflowInterface
{
    /**
     * @var \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface
     */
    protected $availablePaymentMethodsStep;

    /**
     * @var \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStepInterface
     */
    protected $paymentSubFormsFilter;

    /**
     * @param \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface $availablePaymentMethodsStep
     * @param \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStepInterface $paymentSubFormsFilter
     */
    public function __construct(
        AvailablePaymentMethodsStepInterface $availablePaymentMethodsStep,
        PaymentSubFormsFilterStepInterface $paymentSubFormsFilter
    ) {

        $this->availablePaymentMethodsStep = $availablePaymentMethodsStep;
        $this->paymentSubFormsFilter = $paymentSubFormsFilter;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuoteBeforePaymentStep(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $availablePaymentMethods = $this
            ->availablePaymentMethodsStep
            ->getAvailablePaymentMethods($quoteTransfer);

        $quoteTransfer->setAfterpayAvailablePaymentMethods($availablePaymentMethods);

        return $quoteTransfer;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentSubForms
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[]
     */
    public function filterAvailablePaymentMethods(array $paymentSubForms): array
    {
        $filteredPaymentSubForms = $this
            ->paymentSubFormsFilter
            ->filterPaymentSubForms($paymentSubForms);

        return $filteredPaymentSubForms;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addPaymentDataToQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $quoteTransfer = parent::addPaymentDataToQuote($quoteTransfer);
        $this->addAvailablePaymentMethodsDataToPayment($quoteTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function addAvailablePaymentMethodsDataToPayment(QuoteTransfer $quoteTransfer): void
    {
        $paymentTransfer = $quoteTransfer->getPayment();
        $availablePaymentMethodsTransfer = $quoteTransfer->getAfterpayAvailablePaymentMethods();

        $paymentTransfer
            ->setAfterpayCheckoutId($availablePaymentMethodsTransfer->getCheckoutId())
            ->setAfterpayCustomerNumber($availablePaymentMethodsTransfer->getCustomerNumber());
    }
}
