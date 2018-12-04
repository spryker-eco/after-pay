<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps;

use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Yves\Afterpay\AfterpayConfig;
use SprykerEco\Yves\Afterpay\Dependency\Client\AfterpayToQuoteClientInterface;

class PaymentSubFormsFilterStep implements PaymentSubFormsFilterStepInterface
{
    /**
     * @var \SprykerEco\Yves\Afterpay\AfterpayConfig
     */
    protected $config;

    /**
     * @var \SprykerEco\Yves\Afterpay\Dependency\Client\AfterpayToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @param \SprykerEco\Yves\Afterpay\AfterpayConfig $config
     * @param \SprykerEco\Yves\Afterpay\Dependency\Client\AfterpayToQuoteClientInterface $quoteClient
     */
    public function __construct(AfterpayConfig $config, AfterpayToQuoteClientInterface $quoteClient)
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
            ->getAfterpayAvailablePaymentMethods()
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
