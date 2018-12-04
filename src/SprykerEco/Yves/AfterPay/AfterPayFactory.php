<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay;

use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Client\AfterPay\AfterPayClientInterface;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Yves\AfterPay\AuthorizeWorkflow\AfterPayAuthorizeWorkflowInterface;
use SprykerEco\Yves\AfterPay\AuthorizeWorkflow\OneStepAuthorizeWorkflow;
use SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStep;
use SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface;
use SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStep;
use SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStepInterface;
use SprykerEco\Yves\AfterPay\AuthorizeWorkflow\TwoStepsAuthorizeWorkflow;
use SprykerEco\Yves\AfterPay\Dependency\Client\AfterPayToQuoteClientInterface;
use SprykerEco\Yves\AfterPay\Form\DataProvider\InvoiceDataProvider;
use SprykerEco\Yves\AfterPay\Form\InvoiceSubForm;

/**
 * @method \SprykerEco\Yves\AfterPay\AfterPayConfig getConfig()
 */
class AfterPayFactory extends AbstractFactory
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
     * @return \SprykerEco\Yves\AfterPay\AuthorizeWorkflow\AfterPayAuthorizeWorkflowInterface
     */
    public function createAfterPayAuthorizeWorkflow()
    {
        $authorizeWorkflow = $this->getConfig()->getAfterPayAuthorizeWorkflow();

        switch ($authorizeWorkflow) {
            case AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP:
                return $this->createOneStepAuthorizeWorkflow();
            case AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS:
                return $this->createTwoStepsAuthorizeWorkflow();
            default:
                return $this->createOneStepAuthorizeWorkflow();
        }
    }

    /**
     * @return \SprykerEco\Yves\AfterPay\AuthorizeWorkflow\AfterPayAuthorizeWorkflowInterface
     */
    public function createOneStepAuthorizeWorkflow(): AfterPayAuthorizeWorkflowInterface
    {
        return new OneStepAuthorizeWorkflow();
    }

    /**
     * @return \SprykerEco\Yves\AfterPay\AuthorizeWorkflow\AfterPayAuthorizeWorkflowInterface
     */
    public function createTwoStepsAuthorizeWorkflow(): AfterPayAuthorizeWorkflowInterface
    {
        return new TwoStepsAuthorizeWorkflow(
            $this->createAvailablePaymentMethodsStep(),
            $this->createPaymentSubformsFilter()
        );
    }

    /**
     * @return \SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps\AvailablePaymentMethodsStepInterface
     */
    public function createAvailablePaymentMethodsStep(): AvailablePaymentMethodsStepInterface
    {
        return new AvailablePaymentMethodsStep($this->getAfterPayClient());
    }

    /**
     * @return \SprykerEco\Yves\AfterPay\AuthorizeWorkflow\Steps\PaymentSubFormsFilterStepInterface
     */
    public function createPaymentSubformsFilter(): PaymentSubFormsFilterStepInterface
    {
        return new PaymentSubFormsFilterStep(
            $this->getConfig(),
            $this->getQuoteClient()
        );
    }

    /**
     * @return \SprykerEco\Client\AfterPay\AfterPayClientInterface
     */
    public function getAfterPayClient(): AfterPayClientInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::CLIENT_AFTERPAY);
    }

    /**
     * @return \SprykerEco\Yves\AfterPay\Dependency\Client\AfterPayToQuoteClientInterface
     */
    public function getQuoteClient(): AfterPayToQuoteClientInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::CLIENT_QUOTE);
    }
}
