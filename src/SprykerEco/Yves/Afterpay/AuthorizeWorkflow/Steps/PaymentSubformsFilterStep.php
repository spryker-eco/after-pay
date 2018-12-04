<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps;

use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Yves\AfterPay\AfterPayConfig;
use SprykerEco\Yves\AfterPay\Dependency\Client\AfterPayToQuoteClientInterface;

class PaymentSubFormsFilterStep implements PaymentSubFormsFilterStepInterface
{
    /**
     * @var \SprykerEco\Yves\AfterPay\AfterPayConfig
     */
    protected $config;

    /**
     * @var \SprykerEco\Yves\AfterPay\Dependency\Client\AfterPayToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @param \SprykerEco\Yves\AfterPay\AfterPayConfig $config
     * @param \SprykerEco\Yves\AfterPay\Dependency\Client\AfterPayToQuoteClientInterface $quoteClient
     */
    public function __construct(AfterPayConfig $config, AfterPayToQuoteClientInterface $quoteClient)
    {
        $this->config = $config;
        $this->quoteClient = $quoteClient;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentSubForms
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[]
     */
    public function filterPaymentSubForms(array $paymentSubForms): array
    {
        foreach ($paymentSubForms as $key => $subForm) {
            if (!$this->isSubFormPluginAllowed($subForm)) {
                unset($paymentSubForms[$key]);
            }
        }

        return $paymentSubForms;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface $subForm
     *
     * @return bool
     */
    protected function isSubFormPluginAllowed(SubFormInterface $subForm): bool
    {
        $allowedPaymentMethods = $this->getListOfAllowedPaymentMethods();
        $subFormPaymentMethod = $this->getSubFormPaymentMethod($subForm);

        return ($subFormPaymentMethod !== null) && (in_array($subFormPaymentMethod, $allowedPaymentMethods));
    }

    /**
     * @return array
     */
    protected function getListOfAllowedPaymentMethods(): array
    {
        $quoteTransfer = $this->quoteClient->getQuote();

        $allowedPaymentMethodNames = $quoteTransfer
            ->getAfterPayAvailablePaymentMethods()
            ->getAvailablePaymentMethodNames();

        if ($allowedPaymentMethodNames === null) {
            return [];
        }

        return $allowedPaymentMethodNames;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface $subForm
     *
     * @return string|null
     */
    protected function getSubFormPaymentMethod(SubFormInterface $subForm): ?string
    {
        $subFormNameToPaymentMethodMapping = $this->config->getSubFormToPaymentMethodMapping();
        $subFormName = $subForm->getName();

        return $subFormNameToPaymentMethodMapping[$subFormName] ?? null;
    }
}
