<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Filter;

use ArrayObject;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConfig as SharedAfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider\AfterPayPaymentMethodsProviderInterface;

class TwoStepAuthorizePaymentMethodsFilter implements AfterPayPaymentMethodsFilterInterface
{
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
        $result = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if($this->isPaymentProviderAfterPay($paymentMethodTransfer) &&
                !$this->isAvailable($paymentMethodTransfer, $quoteTransfer->getAfterPayAvailablePaymentMethods())) {
                continue;
            }

            $result->append($paymentMethodTransfer);
        }

        $paymentMethodsTransfer->setMethods($result);

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     * @param AfterPayAvailablePaymentMethodsTransfer $availablePaymentMethods
     *
     * @return bool
     */
    protected function isAvailable(
        PaymentMethodTransfer $paymentMethodTransfer,
        AfterPayAvailablePaymentMethodsTransfer $availablePaymentMethods
    ): bool {
        if($availablePaymentMethods->getCheckoutId() === null) {
            return false;
        }

        foreach ($availablePaymentMethods->getAvailablePaymentMethodNames() as $availablePaymentMethodName) {
            if ($paymentMethodTransfer->getMethodName() === SharedAfterPayConfig::PROVIDER_NAME . $availablePaymentMethodName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isPaymentProviderAfterPay(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return strpos($paymentMethodTransfer->getMethodName(), SharedAfterPayConfig::PROVIDER_NAME) !== false;
    }
}
