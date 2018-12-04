<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay\Form;

use Generated\Shared\Transfer\AfterpayPaymentTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\AbstractSubFormType;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormInterface;
use Spryker\Yves\StepEngine\Dependency\Form\SubFormProviderNameInterface;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceSubForm extends AbstractSubFormType implements SubFormInterface, SubFormProviderNameInterface
{
    public const PAYMENT_METHOD = AfterpayConfig::PAYMENT_METHOD_INVOICE;
    public const PAYMENT_PROVIDER = AfterpayConfig::PROVIDER_NAME;

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AfterpayPaymentTransfer::class,
            SubFormInterface::OPTIONS_FIELD_NAME => [],
        ]);
    }

    /**
     * Specifies the property name of the payment transfer object to access the default form data.
     * Form data will be obtained from QuoteTransfer->getPayment()->getAfterpaySofort()
     *
     * @return string
     */
    public function getPropertyPath(): string
    {
        return static::PAYMENT_METHOD;
    }

    /**
     * Using this key, the subform will be registered inside of the twig provider and will be called
     * in payment.twig template.
     *
     * @return string
     */
    public function getName(): string
    {
        return static::PAYMENT_METHOD;
    }

    /**
     * Path to the form template
     *
     * @return string
     */
    public function getTemplatePath(): string
    {
        return static::PAYMENT_PROVIDER . '/' . static::PAYMENT_METHOD;
    }

    /**
     * @return string
     */
    public function getProviderName(): string
    {
        return AfterpayConfig::PROVIDER_NAME;
    }
}
