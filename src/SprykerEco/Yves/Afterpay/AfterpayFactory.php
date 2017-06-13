<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Afterpay;

use Spryker\Yves\Kernel\AbstractFactory;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\OneStepAuthorizeWorkflow;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStep;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubformsFilter;
use SprykerEco\Yves\Afterpay\AuthorizeWorkflow\TwoStepsAuthorizeWorkflow;
use SprykerEco\Yves\Afterpay\Form\InvoiceSubForm;
use SprykerEco\Yves\Afterpay\Form\DataProvider\InvoiceDataProvider;
use SprykerEco\Yves\Afterpay\Checkout\Process\AvailablePaymentMethodsExpander;
use SprykerEco\Yves\Afterpay\FormChecker\AvailablePaymentMethodsSubformFilter;
use SprykerEco\Yves\Afterpay\Handler\AfterpayHandler;

/**
 * @method \SprykerEco\Yves\Afterpay\AfterpayConfig getConfig()
 */
class AfterpayFactory extends AbstractFactory
{

    /**
     * @return \SprykerEco\Yves\Afterpay\Handler\AfterpayHandlerInterface
     */
    public function createAfterpayHandler()
    {
        return new AfterpayHandler(
            $this->getAfterpayClient()
        );
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface
     */
    public function createInvoiceForm()
    {
        return new InvoiceSubForm();
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface
     */
    public function createInvoiceFormDataProvider()
    {
        return new InvoiceDataProvider();
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\AfterpayAuthorizeWorkflowInterface
     */
    public function createAfterpayAuthorizeWorkflow()
    {
        $authorizeWorkflow = $this->getYvesConfig()->getAfterpayAuthorizeWorkflow();

        switch ($authorizeWorkflow) {
            case AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP:
                return $this->createOneStepAuthorizeWorkflow();
            case AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS:
                return $this->createTwoStepsAuthorizeWorkflow();
            default:
                return $this->createOneStepAuthorizeWorkflow();
        }
    }

    /**
     * @return \SprykerEco\Client\Afterpay\AfterpayClientInterface
     */
    public function getAfterpayClient()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::CLIENT_AFTERPAY);
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AfterpayConfig
     */
    public function getYvesConfig()
    {
        return $this->getConfig();
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\AfterpayAuthorizeWorkflowInterface
     */
    protected function createOneStepAuthorizeWorkflow()
    {
        return new OneStepAuthorizeWorkflow();
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\AfterpayAuthorizeWorkflowInterface
     */
    protected function createTwoStepsAuthorizeWorkflow()
    {
        return new TwoStepsAuthorizeWorkflow(
            $this->createAvailablePaymentMethodsStep(),
            $this->createPaymentSubformsFilter()
        );
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface
     */
    protected function createAvailablePaymentMethodsStep()
    {
        return new AvailablePaymentMethodsStep(
            $this->getAfterpayClient()
        );
    }

    /**
     * @return \SprykerEco\Yves\Afterpay\AuthorizeWorkflow\Steps\PaymentSubformsFilterInterface
     */
    protected function createPaymentSubformsFilter()
    {
        return new PaymentSubformsFilter(
            $this->getYvesConfig(),
            $this->getAfterpayClient()
        );
    }

}
