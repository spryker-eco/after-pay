<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps;

use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Client\Afterpay\AfterpayClientInterface;
use SprykerEco\Yves\Afterpay\AfterpayConfig;

class PaymentSubformsFilterStep implements PaymentSubformsFilterStepInterface
{
    /**
     * @var \SprykerEco\Yves\Afterpay\AfterpayConfig
     */
    protected $config;

    /**
     * @var \SprykerEco\Client\Afterpay\AfterpayClientInterface
     */
    protected $afterpayClient;

    /**
     * @param \SprykerEco\Yves\Afterpay\AfterpayConfig $config
     * @param \SprykerEco\Client\Afterpay\AfterpayClientInterface $afterpayClient
     */
    public function __construct(AfterpayConfig $config, AfterpayClientInterface $afterpayClient)
    {
        $this->config = $config;
        $this->afterpayClient = $afterpayClient;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[] $paymentSubforms
     *
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface[]
     */
    public function filterPaymentSubforms(array $paymentSubforms)
    {
        foreach ($paymentSubforms as $key => $subform) {
            if (!$this->isSubformPluginAllowed($subform)) {
                unset($paymentSubforms[$key]);
            }
        }
        return $paymentSubforms;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface $subform
     *
     * @return boolean
     */
    protected function isSubformPluginAllowed(SubFormInterface $subform)
    {
        $allowedPaymentMethods = $this->getListOfAllowedPaymentMethods();
        $subformPaymentMethod = $this->getSubformPaymentMethod($subform);

        return
            ($subformPaymentMethod !== null)
            && (in_array($subformPaymentMethod, $allowedPaymentMethods));
    }

    /**
     * @return array
     */
    protected function getListOfAllowedPaymentMethods()
    {
        $quoteTransfer = $this->afterpayClient->getQuoteFromSession();

        $allowedPaymentMethodNames = $quoteTransfer
            ->getAfterpayAvailablePaymentMethods()
            ->getAvailablePaymentMethodNames();

        if ($allowedPaymentMethodNames === null) {
            return [];
        }

        return $allowedPaymentMethodNames;
    }

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface $subform
     *
     * @return string|null
     */
    protected function getSubformPaymentMethod(SubFormInterface $subform)
    {
        $subformNameToPaymentMethodMapping = $this->config->getSubFormToPaymentMethodMapping();
        $subformName = $subform->getName();

        return $subformNameToPaymentMethodMapping[$subformName] ?? null;
    }
}
