<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider\AfterPayPaymentMethodsProviderInterface;

class AfterPayPaymentMethodsFilter implements AfterPayPaymentMethodsFilterInterface
{
    /**
     * @var \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    protected $availablePaymentMethods;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider\AfterPayPaymentMethodsProviderInterface
     */
    protected $paymentMethodsProvider;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider\AfterPayPaymentMethodsProviderInterface $paymentMethodsProvider
     */
    public function __construct(AfterPayPaymentMethodsProviderInterface $paymentMethodsProvider)
    {
        $this->paymentMethodsProvider = $paymentMethodsProvider;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer {
        $this->availablePaymentMethods = $this->paymentMethodsProvider->getAvailablePaymentMethods($quoteTransfer);

        $result = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethod) {
            if ($this->isPaymentProviderAfterPay($paymentMethod) && !$this->isAvailable($paymentMethod)) {
                continue;
            }

            $result->append($paymentMethod);
        }

        $paymentMethodsTransfer->setMethods($result);

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isAvailable(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return in_array(
            $paymentMethodTransfer->getMethodName(),
            $this->availablePaymentMethods->getAvailablePaymentMethodNames(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isPaymentProviderAfterPay(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return strpos($paymentMethodTransfer->getMethodName(), AfterPayConfig::PROVIDER_NAME) !== false;
    }
}
