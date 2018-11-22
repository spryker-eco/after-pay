<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SalesPaymentTransfer;
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
    public function getPriceToPayForOrder(OrderTransfer $orderWithPaymentTransfer)
    {
        $salesPaymentTransfer = $this->createSalesPaymentTransfer($orderWithPaymentTransfer);

        return $this->paymentFacade->getPaymentMethodPriceToPay($salesPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\SalesPaymentTransfer
     */
    protected function createSalesPaymentTransfer(OrderTransfer $orderWithPaymentTransfer)
    {
        $salesPaymentTransfer = new SalesPaymentTransfer();
        $salesPaymentTransfer->setPaymentProvider(AfterpayConstants::PROVIDER_NAME);
        $salesPaymentTransfer->setPaymentMethod($this->findPaymentMethod($orderWithPaymentTransfer));
        $salesPaymentTransfer->setFkSalesOrder($orderWithPaymentTransfer->getIdSalesOrder());

        return $salesPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return null|string
     */
    protected function findPaymentMethod(OrderTransfer $orderWithPaymentTransfer)
    {
        foreach ($orderWithPaymentTransfer->getPayments() as $paymentTransfer) {
            if ($paymentTransfer->getPaymentProvider() === AfterpayConstants::PROVIDER_NAME) {
                return $paymentTransfer->getPaymentMethod();
            }
        }

        return null;
    }
}
