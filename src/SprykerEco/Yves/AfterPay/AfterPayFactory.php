<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay;

use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use SprykerEco\Yves\AfterPay\Expander\AfterPayPaymentExpander;
use SprykerEco\Yves\AfterPay\Expander\AfterPayPaymentExpanderInterface;
use SprykerEco\Yves\AfterPay\Form\DataProvider\InvoiceDataProvider;
use SprykerEco\Yves\AfterPay\Form\InvoiceSubForm;

/**
 * @method \SprykerEco\Yves\AfterPay\AfterPayConfig getConfig()
 * @method \SprykerEco\Client\AfterPay\AfterPayClientInterface getClient()
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
     * @return \SprykerEco\Yves\AfterPay\Expander\AfterPayPaymentExpanderInterface
     */
    public function createPaymentExpander(): AfterPayPaymentExpanderInterface
    {
        return new AfterPayPaymentExpander();
    }
}
