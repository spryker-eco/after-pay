<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SalesPaymentTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface;

class PriceToPayProvider implements PriceToPayProviderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface
     */
    protected $paymentFacade;

    /**
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface $paymentFacade
     */
    public function __construct(AfterPayToPaymentFacadeInterface $paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return int
     */
    public function getPriceToPayForOrder(OrderTransfer $orderWithPaymentTransfer): int
    {
        $salesPaymentTransfer = $this->createSalesPaymentTransfer($orderWithPaymentTransfer);

        return $this->paymentFacade->getPaymentMethodPriceToPay($salesPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\SalesPaymentTransfer
     */
    protected function createSalesPaymentTransfer(OrderTransfer $orderWithPaymentTransfer): SalesPaymentTransfer
    {
        $salesPaymentTransfer = new SalesPaymentTransfer();
        $salesPaymentTransfer->setPaymentProvider(AfterPayConfig::PROVIDER_NAME);
        $salesPaymentTransfer->setPaymentMethod($this->findPaymentMethod($orderWithPaymentTransfer));
        $salesPaymentTransfer->setFkSalesOrder($orderWithPaymentTransfer->getIdSalesOrder());

        return $salesPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return string|null
     */
    protected function findPaymentMethod(OrderTransfer $orderWithPaymentTransfer): ?string
    {
        foreach ($orderWithPaymentTransfer->getPayments() as $paymentTransfer) {
            if ($paymentTransfer->getPaymentProvider() === AfterPayConfig::PROVIDER_NAME) {
                return $paymentTransfer->getPaymentMethod();
            }
        }

        return null;
    }
}
