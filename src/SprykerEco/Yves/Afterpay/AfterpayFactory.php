<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay;

use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Client\Afterpay\AfterpayClientInterface;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\AfterpayAuthorizeWorkflowInterface;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\OneStepAuthorizeWorkflow;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStep;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStep;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStepInterface;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\TwoStepsAuthorizeWorkflow;
use SprykerEco\Yves\Afterpay\Dependency\Client\AfterpayToQuoteClientInterface;
use SprykerEco\Yves\Afterpay\Form\DataProvider\InvoiceDataProvider;
use SprykerEco\Yves\Afterpay\Form\InvoiceSubForm;

/**
 * @method \SprykerEco\Yves\Afterpay\AfterpayConfig getConfig()
 */
class AfterpayFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createInvoiceForm(): SubFormInterface
    {
        return new InvoiceSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createInvoiceFormDataProvider(): StepEngineFormDataProviderInterface
    {
        return new InvoiceDataProvider();
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\AfterpayAuthorizeWorkflowInterface
     */
    public function createAfterpayAuthorizeWorkflow()
    {
        $authorizeWorkflow = $this->getConfig()->getAfterpayAuthorizeWorkflow();

        switch ($authorizeWorkflow) {
            case AfterpayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP:
                return $this->createOneStepAuthorizeWorkflow();
            case AfterpayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS:
                return $this->createTwoStepsAuthorizeWorkflow();
            default:
                return $this->createOneStepAuthorizeWorkflow();
        }
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\AfterpayAuthorizeWorkflowInterface
     */
    public function createOneStepAuthorizeWorkflow(): AfterpayAuthorizeWorkflowInterface
    {
        return new OneStepAuthorizeWorkflow();
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\AfterpayAuthorizeWorkflowInterface
     */
    public function createTwoStepsAuthorizeWorkflow(): AfterpayAuthorizeWorkflowInterface
    {
        return new TwoStepsAuthorizeWorkflow(
            $this->createAvailablePaymentMethodsStep(),
            $this->createPaymentSubformsFilter()
        );
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface
     */
    public function createAvailablePaymentMethodsStep(): AvailablePaymentMethodsStepInterface
    {
        return new AvailablePaymentMethodsStep($this->getAfterpayClient());
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStepInterface
     */
    public function createPaymentSubformsFilter(): PaymentSubFormsFilterStepInterface
    {
        return new PaymentSubFormsFilterStep(
            $this->getConfig(),
            $this->getQuoteClient()
        );
    }

    /**
     * @return \SprykerEco\Client\Afterpay\AfterpayClientInterface
     */
    public function getAfterpayClient(): AfterpayClientInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::CLIENT_AFTERPAY);
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\Dependency\Client\AfterpayToQuoteClientInterface
     */
    public function getQuoteClient(): AfterpayToQuoteClientInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::CLIENT_QUOTE);
    }
}
