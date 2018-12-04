<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\Plugin;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use Spryker\Yves\StepEngine\Dependency\Plugin\Handler\StepHandlerPluginInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerEco\Yves\Afterpay\AfterpayFactory getFactory()
 */
class AfterpayHandlerPlugin extends AbstractPlugin implements
    StepHandlerPluginInterface,
    PaymentSubFormFilterPluginInterface,
    PrePaymentQuoteExpanderPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expandQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        return $this
            ->getFactory()
            ->createAfterpayAuthorizeWorkflow()
            ->expandQuoteBeforePaymentStep($quoteTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentSubForms
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[]
     */
    public function filterPaymentSubForms(array $paymentSubForms): array
    {
        return $this
            ->getFactory()
            ->createAfterpayAuthorizeWorkflow()
            ->filterAvailablePaymentMethods($paymentSubForms);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addToDataClass(Request $request, AbstractTransfer $quoteTransfer): QuoteTransfer
    {
        return $this
            ->getFactory()
            ->createAfterpayAuthorizeWorkflow()
            ->addPaymentDataToQuote($quoteTransfer);
    }
}
