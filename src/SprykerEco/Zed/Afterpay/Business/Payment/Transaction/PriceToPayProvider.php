<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SalesPaymentTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToPaymentInterface;

class PriceToPayProvider implements PriceToPayProviderInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToPaymentInterface
     */
    protected $paymentFacade;

    /**
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToPaymentInterface $paymentFacade
     */
    public function __construct(AfterpayToPaymentInterface $paymentFacade)
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
        $salesPaymentTransfer->setPaymentProvider(AfterpayConfig::PROVIDER_NAME);
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
            if ($paymentTransfer->getPaymentProvider() === AfterpayConfig::PROVIDER_NAME) {
                return $paymentTransfer->getPaymentMethod();
            }
        }

        return null;
    }
}
